<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Handle payment callback
     */
    public function callback(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'status' => 'required|in:success,failed'
        ]);

        $payment = Payment::where('reference', $request->reference)->first();

        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'Payment not found.');
        }

        if ($payment->status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Payment has already been processed.');
        }

        DB::beginTransaction();
        try {
            if ($request->status === 'success') {
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Credit wallet if this is wallet funding
                if ($payment->type === 'wallet_funding') {
                    $wallet = $payment->user->wallet;
                    $wallet->credit(
                        $payment->amount,
                        'wallet_funding',
                        "Wallet funded via {$payment->gateway}",
                        ['payment_reference' => $payment->reference]
                    );

                    DB::commit();
                    return redirect()->route('wallet')->with('success', 'Wallet funded successfully!');
                }

                // Handle subscription payment
                if ($payment->type === 'subscription' && $payment->subscription_id) {
                    $subscription = $payment->subscription;
                    $subscription->update([
                        'status' => 'active',
                        'payment_reference' => $payment->reference
                    ]);

                    DB::commit();
                    return redirect()->route('subscriptions.history')->with('success', 'Subscription activated successfully!');
                }

                DB::commit();
                return redirect()->route('dashboard')->with('success', 'Payment completed successfully!');

            } else {
                // Payment failed
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                ]);

                // If this was a subscription payment, mark subscription as failed
                if ($payment->type === 'subscription' && $payment->subscription_id) {
                    $subscription = $payment->subscription;
                    $subscription->update(['status' => 'failed']);
                }

                DB::commit();
                return redirect()->route('dashboard')->with('error', 'Payment failed. Please try again.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('dashboard')->with('error', 'An error occurred while processing payment. Please contact support.');
        }
    }
}
