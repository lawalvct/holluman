<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create subscription plans
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => '10Mbps speed with 50GB data limit',
                'price' => 2500.00,
                'speed' => '10Mbps',
                'data_limit' => '50GB',
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Standard Plan',
                'description' => '25Mbps speed with 100GB data limit',
                'price' => 5000.00,
                'speed' => '25Mbps',
                'data_limit' => '100GB',
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Premium Plan',
                'description' => '50Mbps speed with unlimited data',
                'price' => 10000.00,
                'speed' => '50Mbps',
                'data_limit' => 'Unlimited',
                'duration_days' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['name' => $plan['name']], $plan);
        }

        // Create sample users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '08012345678',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '08087654321',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'phone' => '08055566677',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => false,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(['email' => $userData['email']], $userData);

            // Create wallet for user
            if (!$user->wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => rand(1000, 50000),
                ]);
            }
        }

        // Create sample subscriptions and payments
        $allUsers = User::where('role', 'user')->get();
        $allPlans = SubscriptionPlan::all();

        foreach ($allUsers->take(2) as $user) {
            $plan = $allPlans->random();

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'start_date' => now()->subDays(rand(1, 20)),
                'end_date' => now()->addDays(rand(10, 30)),
                'amount_paid' => $plan->price,
                'payment_method' => 'paystack',
                'plan_snapshot' => json_encode([
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'speed' => $plan->speed,
                    'data_limit' => $plan->data_limit,
                    'duration_days' => $plan->duration_days,
                ]),
                'status' => 'active',
            ]);

            // Create payment record
            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $plan->price,
                'gateway' => 'paystack',
                'type' => 'subscription',
                'status' => 'successful',
                'reference' => 'REF_' . strtoupper(uniqid()),
                'gateway_response' => json_encode(['status' => 'success']),
            ]);

            // Create wallet transaction
            $wallet = $user->wallet;
            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore - $plan->price;

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'reference' => 'TXN_' . strtoupper(uniqid()),
                'type' => 'debit',
                'amount' => $plan->price,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Subscription payment for ' . $plan->name,
                'status' => 'completed',
            ]);

            // Update wallet balance
            $wallet->update(['balance' => $balanceAfter]);
        }

        // Create additional payment records
        foreach ($allUsers as $user) {
            // Random wallet credit
            if (rand(1, 100) > 50) {
                $creditAmount = rand(1000, 10000);

                Payment::create([
                    'user_id' => $user->id,
                    'amount' => $creditAmount,
                    'gateway' => rand(1, 100) > 50 ? 'paystack' : 'nomba',
                    'type' => 'wallet_funding',
                    'status' => 'successful',
                    'reference' => 'FUND_' . strtoupper(uniqid()),
                    'gateway_response' => json_encode(['status' => 'success']),
                ]);

                WalletTransaction::create([
                    'wallet_id' => $user->wallet->id,
                    'user_id' => $user->id,
                    'reference' => 'CREDIT_' . strtoupper(uniqid()),
                    'type' => 'credit',
                    'amount' => $creditAmount,
                    'balance_before' => $user->wallet->balance,
                    'balance_after' => $user->wallet->balance + $creditAmount,
                    'description' => 'Wallet funding via payment gateway',
                    'payment_method' => rand(1, 100) > 50 ? 'paystack' : 'nomba',
                    'status' => 'completed',
                ]);

                // Update wallet balance
                $user->wallet->increment('balance', $creditAmount);
            }
        }

        $this->command->info('Sample data seeded successfully!');
    }
}
