<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display welcome page with available plans
     */
    public function welcome()
    {
        $plans = SubscriptionPlan::active()->ordered()->take(4)->get();
        return view('welcome', compact('plans'));
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
        ]);

        $user = Auth::user();

        // Check if user already has an active subscription
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription) {
            return back()->with('error', 'You already have an active subscription. Please wait for it to expire or contact support.');
        }

        DB::beginTransaction();
        try {
            if ($request->payment_method === 'wallet') {
                // Pay with wallet
                $wallet = $user->wallet;

                if (!$wallet->hasSufficientBalance($plan->price)) {
                    return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
                }

                // Create subscription
                $subscription = $this->createSubscription($user, $plan, 'wallet');

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

                DB::commit();
                return redirect()->route('subscriptions.history')->with('success', 'Subscription activated successfully!');

            } else {
                // Pay with payment gateway (Paystack/Nomba)
                $subscription = $this->createSubscription($user, $plan, $request->payment_method);

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
            ->with('subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $activeSubscription = $user->activeSubscription();

        return view('subscriptions.history', compact('subscriptions', 'activeSubscription'));
    }

    /**
     * Create a new subscription
     */
    private function createSubscription($user, $plan, $paymentMethod)
    {
        $startDate = now();
        $endDate = $startDate->copy()->addDays($plan->duration_days);

        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount_paid' => $plan->price,
            'payment_method' => $paymentMethod,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
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
