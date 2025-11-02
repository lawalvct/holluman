<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Wallet;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display welcome page with available plans
     */
    public function welcome()
    {
        $plans = SubscriptionPlan::active()->ordered()->take(4)->get();
        $companySettings = SettingsHelper::getCompanySettings();
        $metaSettings = SettingsHelper::getMetaSettings();

        return view('welcome', compact('plans', 'companySettings', 'metaSettings'));
    }

    /**
     * Display all subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('subscriptions.plans', compact('plans'));
    }

    /**
     * Show a specific subscription plan
     */
    public function show(SubscriptionPlan $plan)
    {
        if (!$plan->is_active) {
            return redirect()->route('plans')->with('error', 'This plan is not available.');
        }

        $user = Auth::user();
        $userWallet = $user->wallet;
        $activeSubscription = $user->activeSubscription();

        return view('subscriptions.show', compact('plan', 'userWallet', 'activeSubscription'));
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,paystack,nomba',
            'network_id' => 'required|exists:networks,id',
            'subscriber_phone' => 'required|string|regex:/^[0-9]{11}$/',
        ], [
            'subscriber_phone.regex' => 'Phone number must be 11 digits.',
            'network_id.required' => 'Please select a network provider.',
            'network_id.exists' => 'Selected network provider is invalid.',
        ]);

        $user = Auth::user();

        // Check if user already has an active subscription
        $activeSubscription = $user->activeSubscription();
        // if ($activeSubscription) {
        //     return back()->with('error', 'You already have an active subscription. Please wait for it to expire or contact support.');
        // }

        DB::beginTransaction();
        try {
            if ($request->payment_method === 'wallet') {
                // Pay with wallet
                $wallet = $user->wallet;

                if (!$wallet->hasSufficientBalance($plan->price)) {
                    return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
                }

                // Create subscription
                $subscription = $this->createSubscription($user, $plan, 'wallet', $request->network_id, $request->subscriber_phone);

                // Debit wallet
                $wallet->debit($plan->price, "Subscription payment for {$plan->name}", [
                    'subscription_id' => $subscription->id,
                    'plan_name' => $plan->name,
                ]);

                // Update subscription status
                $subscription->update([
                    'status' => 'active',
                    'payment_reference' => 'WALLET_' . time(),
                ]);

                // Activate actual data subscription with N3tdata
                $dataActivationResult = $this->activateDataSubscription($subscription);

                DB::commit();

                if ($dataActivationResult['success']) {
                    return redirect()->route('subscriptions.history')
                        ->with('success', 'Subscription activated successfully! ' . $dataActivationResult['message']);
                } else {
                    return redirect()->route('subscriptions.history')
                        ->with('warning', 'Subscription created but data activation failed: ' . $dataActivationResult['message'] . '. Please contact support.');
                }

            } else {

                // Pay with payment gateway (Paystack/Nomba)
                // Log the parameters before creating subscription
                Log::info('Creating subscription with payment gateway', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'payment_method' => $request->payment_method,
                    'network_id' => $request->network_id,
                    'subscriber_phone' => $request->subscriber_phone,
                ]);

               $subscription = $this->createSubscription($user, $plan, $request->payment_method, $request->network_id, $request->subscriber_phone);

                // Create payment record
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'reference' => 'PAY_' . time() . '_' . uniqid(),
                    'amount' => $plan->price,
                    'currency' => 'NGN',
                    'gateway' => $request->payment_method,
                    'type' => 'subscription',
                    'subscription_id' => $subscription->id,
                    'status' => 'pending',
                ]);

                DB::commit();

                // Redirect to payment gateway
                return $this->initializePayment($payment, $request->payment_method);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while processing your subscription. Please try again.');
        }
    }

    /**
     * User's subscription history
     */
    public function history()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
            ->with(['subscriptionPlan', 'network'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription) {
            $activeSubscription->load('network');
        }

        return view('subscriptions.history', compact('subscriptions', 'activeSubscription'));
    }

    /**
     * Create a new subscription
     */
    private function createSubscription($user, $plan, $paymentMethod, $networkId, $subscriberPhone)
    {
        $startDate = now();
        $endDate = $startDate->copy()->addDays($plan->duration_days);

        // Calculate total months based on duration_days
        $monthsTotal = 1; // Default to 1 month
        $needsRenewal = false;

        // if ($plan->duration_days >= 365) { // 1 year
        //     $monthsTotal = 12;
        //     $needsRenewal = true;
        // } elseif ($plan->duration_days >= 180) { // 6 months
        //     $monthsTotal = 6;
        //     $needsRenewal = true;
        // } elseif ($plan->duration_days >= 90) { // 3 months
        //     $monthsTotal = 3;
        //     $needsRenewal = true;
        // } elseif ($plan->duration_days >= 60) { // 2 months
        //     $monthsTotal = 2;
        //     $needsRenewal = true;
        // }

                if ($plan->duration_days >= 336) { // 1 year
            $monthsTotal = 12;
            $needsRenewal = true;
        } elseif ($plan->duration_days >= 168) { // 6 months
            $monthsTotal = 6;
            $needsRenewal = true;
        } elseif ($plan->duration_days >= 84) { // 3 months
            $monthsTotal = 3;
            $needsRenewal = true;
        } elseif ($plan->duration_days >= 56) { // 2 months
            $monthsTotal = 2;
            $needsRenewal = true;
        }

        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'network_id' => $networkId,
            'subscriber_phone' => $subscriberPhone,
            'amount_paid' => $plan->price,
            'payment_method' => $paymentMethod,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'months_total' => $monthsTotal,
            'months_activated' => 0,
            'needs_renewal' => $needsRenewal,
            'plan_snapshot' => [
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'duration_days' => $plan->duration_days,
                'speed' => $plan->speed,
                'data_limit' => $plan->data_limit,
                'features' => $plan->features,
            ],
        ]);
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
     * Handle Nomba payment callback for subscriptions
     */
    public function handleNombaCallback(Request $request)
    {
        try {
            $reference = $request->query('reference') ?? $request->input('orderId');

            if (!$reference) {
                Log::warning('Nomba subscription callback received without reference');
                return redirect()->route('plans')
                    ->with('error', 'Payment verification failed: No reference provided');
            }

            // Find payment by reference
            $payment = Payment::where('reference', $reference)
                ->orWhere('gateway_reference', $reference)
                ->where('type', 'subscription')
                ->first();

            if (!$payment) {
                Log::warning('Subscription payment not found for reference', ['reference' => $reference]);
                return redirect()->route('plans')
                    ->with('error', 'Payment not found');
            }

            // Verify payment with Nomba
            $nombaHelper = new \App\Helpers\NombaPyamentHelper();
            $verificationResult = $nombaHelper->verifyPaymentByReference($reference);

            if ($verificationResult['success'] && $verificationResult['payment_status'] === 'successful') {
                // Process successful subscription payment and activate data subscription
                $subscriptionResult = $this->processSuccessfulSubscriptionPayment($payment, $verificationResult['data']);

                if ($subscriptionResult['success']) {
                    return redirect()->route('subscriptions.history')
                        ->with('success', 'Subscription activated successfully! Payment of ₦' . number_format($payment->amount, 2) . ' completed. ' . $subscriptionResult['message']);
                } else {
                    return redirect()->route('subscriptions.history')
                        ->with('warning', 'Payment successful but data activation failed: ' . $subscriptionResult['message'] . '. Please contact support.');
                }
            } else {
                // Handle failed payment
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($verificationResult)
                ]);

                Log::info('Subscription payment verification failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'verification_result' => $verificationResult
                ]);

                return redirect()->route('plans')
                    ->with('error', 'Payment verification failed. Please try again or contact support.');
            }

        } catch (\Exception $e) {
            Log::error('Nomba subscription callback processing exception', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return redirect()->route('plans')
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }

    /**
     * Process successful subscription payment
     */
    private function processSuccessfulSubscriptionPayment($payment, $gatewayData = null)
    {
        DB::beginTransaction();
        try {
            // Update payment status
            $payment->update([
                'status' => 'completed',
                'gateway_response' => json_encode($gatewayData),
                'paid_at' => now()
            ]);

            // Find and activate the subscription
            $subscription = Subscription::find($payment->subscription_id);
            if (!$subscription) {
                throw new \Exception('Subscription not found');
            }

            $subscription->update([
                'status' => 'active',
                'payment_reference' => $payment->reference,
            ]);

            // Activate actual data subscription with N3tdata
            $dataActivationResult = $this->activateDataSubscription($subscription);

            DB::commit();

            Log::info('Subscription payment completed successfully', [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
                'user_id' => $payment->user_id,
                'amount' => $payment->amount,
                'data_activation' => $dataActivationResult
            ]);

            return $dataActivationResult;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to process successful subscription payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment processed but subscription activation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Activate data subscription using N3tdata API
     */
    private function activateDataSubscription($subscription)
    {
        try {
            $n3tDataHelper = new \App\Helpers\N3tDataHelper();

            // Map local network ID to N3tdata network ID
            $n3tNetworkId = $subscription->network_id;

            // Map subscription plan to N3tdata data plan ID using network's n3tdata_plainid
            $dataPlanId = $n3tDataHelper->mapDataPlanId($subscription->subscriptionPlan, $subscription->network_id);

            // Generate unique request ID
            $requestId = 'SUB_' . $subscription->id . '_' . time();

            // Purchase data subscription
            $result = $n3tDataHelper->purchaseDataSubscription(
                $n3tNetworkId,
                $subscription->subscriber_phone,
                $dataPlanId,
                $requestId
            );

            if ($result['success']) {
                // Parse N3tdata response for display columns
                $responseData = $result['data'] ?? [];

                // Calculate next renewal date (1 month from now) if subscription needs renewal
                $nextRenewalDate = null;
                $newMonthsActivated = $subscription->months_activated + 1;

                if ($subscription->needs_renewal && $newMonthsActivated < $subscription->months_total) {
                    $nextRenewalDate = now()->addMonth();
                }

                // Update subscription with N3tdata response and parsed data
                $subscription->update([
                    'n3tdata_request_id' => $requestId,
                    'n3tdata_response' => json_encode($result['data']),
                    'data_activated_at' => now(),
                    'n3tdata_status' => $responseData['status'] ?? null,
                    'n3tdata_plan' => $responseData['dataplan'] ?? null,
                    'n3tdata_amount' => isset($responseData['amount']) ? (float)$responseData['amount'] : null,
                    'n3tdata_phone_number' => $responseData['phone_number'] ?? null,
                    'months_activated' => $newMonthsActivated,
                    'last_n3tdata_activation_date' => now(),
                    'next_renewal_due_date' => $nextRenewalDate,
                ]);

                return [
                    'success' => true,
                    'message' => 'Data subscription activated successfully on ' . ($result['data']['network'] ?? 'network') .
                                 ($subscription->needs_renewal ? " (Month {$newMonthsActivated}/{$subscription->months_total})" : '')
                ];
            } else {
                // Parse failed response data
                $responseData = $result['data'] ?? [];

                // Log the failure but don't fail the entire process
                Log::error('N3tdata activation failed for subscription', [
                    'subscription_id' => $subscription->id,
                    'phone' => $subscription->subscriber_phone,
                    'error' => $result['message']
                ]);

                // Update subscription with failure info and parsed data
                $subscription->update([
                    'n3tdata_request_id' => $requestId,
                    'n3tdata_response' => json_encode($result),
                    'data_activation_failed_at' => now(),
                    'n3tdata_status' => $responseData['status'] ?? 'fail',
                    'n3tdata_plan' => $responseData['dataplan'] ?? null,
                    'n3tdata_amount' =>  isset($responseData['amount']) ? (float)$responseData['amount'] : null,
                    'n3tdata_phone_number' => $subscription->subscriber_phone,
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Data activation failed'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception during data subscription activation', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Technical error during data activation: ' . $e->getMessage()
            ];
        }
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
            $nombaHelper = new \App\Helpers\NombaPyamentHelper();

            // Prepare payment data
            $paymentData = [
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'email' => $payment->user->email,
                'callback_url' => route('payment.callback.nomba.subscription'),
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
                Log::error('Nomba subscription payment initialization failed', [
                    'payment_id' => $payment->id,
                    'error' => $result['message']
                ]);

                return back()->with('error', 'Payment initialization failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Nomba subscription payment initialization exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'An error occurred while initializing payment. Please try again.');
        }
    }
}
