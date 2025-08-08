<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return view('wallet.index', compact('wallet', 'transactions'));
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
        // For now, we'll create a simple form that simulates payment
        // In production, you would integrate with actual Nomba API
        return view('payments.nomba', compact('payment'));
    }
}
