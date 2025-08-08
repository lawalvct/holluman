<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\NombaPyamentHelper;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * Display wallet dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        // Get recent transactions
        $transactions = $user->walletTransactions()
            ->latest()
            ->paginate(15);

        // Check if Nomba is configured
        $nombaConfigured = \App\Models\Setting::isNombaConfigured();

        return view('wallet.index', compact('wallet', 'transactions', 'nombaConfigured'));
    }

    /**
     * Fund wallet
     */
    public function fund(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:1000000',
            'gateway' => 'required|in:paystack,nomba',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'reference' => 'FUND_' . time() . '_' . uniqid(),
                'amount' => $request->amount,
                'currency' => 'NGN',
                'gateway' => $request->gateway,
                'type' => 'wallet_funding',
                'status' => 'pending',
            ]);

            DB::commit();

            // Redirect to payment gateway
            return $this->initializePayment($payment, $request->gateway);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while processing your payment. Please try again.');
        }
    }

    /**
     * Display wallet transactions
     */
    public function transactions()
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()
            ->latest()
            ->paginate(20);

        return view('wallet.transactions', compact('transactions'));
    }

    /**
     * Initialize payment with gateway
     */
    private function initializePayment($payment, $gateway)
    {
        if ($gateway === 'paystack') {
            return $this->initializePaystackPayment($payment);
        } elseif ($gateway === 'nomba') {
            return $this->initializeNombaPayment($payment);
        }

        return back()->with('error', 'Invalid payment gateway selected.');
    }

    /**
     * Initialize Paystack payment
     */
    private function initializePaystackPayment($payment)
    {
        // For now, we'll create a simple form that simulates payment
        // In production, you would integrate with actual Paystack API
        return view('payments.paystack', compact('payment'));
    }

    /**
     * Initialize Nomba payment
     */
    private function initializeNombaPayment($payment)
    {
        try {
            $nombaHelper = new NombaPyamentHelper();

            // Prepare payment data
            $paymentData = [
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'email' => $payment->user->email,
                'callback_url' => route('payment.callback.nomba'),
                'reference' => $payment->reference
            ];

            // Initiate payment with Nomba
            $result = $nombaHelper->initiatePayment($paymentData);

            if ($result['success']) {
                // Update payment record with gateway reference
                $payment->update([
                    'gateway_reference' => $result['data']['reference'],
                    'gateway_response' => json_encode($result['data'])
                ]);

                // Redirect to Nomba checkout
                return redirect($result['data']['checkout_url']);
            } else {
                Log::error('Nomba payment initialization failed', [
                    'payment_id' => $payment->id,
                    'error' => $result['message']
                ]);

                return back()->with('error', 'Payment initialization failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Nomba payment initialization exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'An error occurred while initializing payment. Please try again.');
        }
    }

    /**
     * Handle Nomba payment callback
     */
    public function handleNombaCallback(Request $request)
    {
        try {
            $reference = $request->query('reference') ?? $request->input('orderId');

            if (!$reference) {
                Log::warning('Nomba callback received without reference');
                return redirect()->route('wallet')
                    ->with('error', 'Payment verification failed: No reference provided');
            }

            // Find payment by reference
            $payment = Payment::where('reference', $reference)
                ->orWhere('gateway_reference', $reference)
                ->first();

            if (!$payment) {
                Log::warning('Payment not found for reference', ['reference' => $reference]);
                return redirect()->route('wallet')
                    ->with('error', 'Payment not found');
            }

            // Verify payment with Nomba
            $nombaHelper = new NombaPyamentHelper();
            $verificationResult = $nombaHelper->verifyPaymentByReference($reference);

            if ($verificationResult['success'] && $verificationResult['payment_status'] === 'successful') {
                // Process successful payment
                $this->processSuccessfulPayment($payment, $verificationResult['data']);

                return redirect()->route('wallet')
                    ->with('success', 'Payment successful! Your wallet has been funded with ₦' . number_format($payment->amount, 2));
            } else {
                // Handle failed payment
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($verificationResult)
                ]);

                Log::info('Payment verification failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'verification_result' => $verificationResult
                ]);

                return redirect()->route('wallet')
                    ->with('error', 'Payment verification failed. Please try again or contact support.');
            }

        } catch (\Exception $e) {
            Log::error('Nomba callback processing exception', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return redirect()->route('wallet')
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }

    /**
     * Process successful payment and update wallet
     */
    private function processSuccessfulPayment($payment, $gatewayData = null)
    {
        DB::beginTransaction();
        try {
            // Update payment status
            $payment->update([
                'status' => 'completed',
                'gateway_response' => json_encode($gatewayData),
                'paid_at' => now()
            ]);

            // Get or create user wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $payment->user_id],
                ['balance' => 0]
            );

            // Add funds to wallet
            $wallet->increment('balance', $payment->amount);

            // Create wallet transaction record
            WalletTransaction::create([
                'user_id' => $payment->user_id,
                'wallet_id' => $wallet->id,
                'payment_id' => $payment->id,
                'type' => 'credit',
                'amount' => $payment->amount,
                'description' => 'Wallet funding via ' . ucfirst($payment->gateway),
                'reference' => $payment->reference,
                'status' => 'completed'
            ]);

            DB::commit();

            Log::info('Wallet funding completed successfully', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'amount' => $payment->amount,
                'new_balance' => $wallet->fresh()->balance
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to process successful payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle Nomba webhook notifications
     */
    public function handleNombaWebhook(Request $request)
    {
        try {
            $nombaHelper = new NombaPyamentHelper();

            // Verify webhook signature
            if (!$nombaHelper->verifyWebhookSignatureFromRequest($request)) {
                Log::warning('Invalid Nomba webhook signature');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $payload = $request->all();

            // Extract payment reference from webhook payload
            $reference = $payload['data']['orderReference'] ?? $payload['orderReference'] ?? null;

            if (!$reference) {
                Log::warning('Nomba webhook received without reference', $payload);
                return response()->json(['error' => 'No reference found'], 400);
            }

            // Find payment
            $payment = Payment::where('reference', $reference)
                ->orWhere('gateway_reference', $reference)
                ->first();

            if (!$payment) {
                Log::warning('Payment not found for webhook reference', ['reference' => $reference]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Only process if payment is still pending
            if ($payment->status !== 'pending') {
                Log::info('Payment already processed', ['payment_id' => $payment->id, 'status' => $payment->status]);
                return response()->json(['message' => 'Payment already processed'], 200);
            }

            // Verify payment status with Nomba API
            $verificationResult = $nombaHelper->verifyPaymentByReference($reference);

            if ($verificationResult['success'] && $verificationResult['payment_status'] === 'successful') {
                $this->processSuccessfulPayment($payment, $verificationResult['data']);

                Log::info('Webhook payment processed successfully', [
                    'payment_id' => $payment->id,
                    'reference' => $reference
                ]);

                return response()->json(['message' => 'Payment processed successfully'], 200);
            } else {
                // Update payment as failed
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($verificationResult)
                ]);

                Log::info('Webhook payment verification failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'verification_result' => $verificationResult
                ]);

                return response()->json(['message' => 'Payment verification failed'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Nomba webhook processing exception', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}
