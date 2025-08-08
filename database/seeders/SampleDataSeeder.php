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
use App\Models\Network;
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

        // Create sample networks (Nigerian ISP providers)
        $networks = [
            [
                'name' => 'MTN',
                'code' => 'MTN',
                'full_name' => 'Mobile Telephone Networks Nigeria',
                'description' => 'Leading telecommunications provider in Nigeria offering mobile, broadband, and enterprise services.',
                'color' => '#FFCC02',
                'type' => 'mobile',
                'is_active' => true,
                'coverage_percentage' => 95.5,
                'service_areas' => ['Lagos', 'Abuja', 'Kano', 'Port Harcourt', 'Ibadan', 'Kaduna', 'Benin City'],
                'contact_info' => [
                    'customer_service' => '180',
                    'website' => 'https://www.mtnonline.com',
                    'email' => 'customercare@mtnonline.com'
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Airtel',
                'code' => 'ART',
                'full_name' => 'Airtel Networks Limited',
                'description' => 'Premier telecommunications network providing innovative mobile and data services across Nigeria.',
                'color' => '#E31E24',
                'type' => 'mobile',
                'is_active' => true,
                'coverage_percentage' => 92.3,
                'service_areas' => ['Lagos', 'Abuja', 'Kano', 'Ibadan', 'Port Harcourt', 'Kaduna', 'Maiduguri'],
                'contact_info' => [
                    'customer_service' => '111',
                    'website' => 'https://www.airtel.com.ng',
                    'email' => 'care@ng.airtel.com'
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Glo',
                'code' => 'GLO',
                'full_name' => 'Globacom Limited',
                'description' => 'Indigenous telecommunications company offering affordable mobile and broadband services.',
                'color' => '#00A651',
                'type' => 'mobile',
                'is_active' => true,
                'coverage_percentage' => 88.7,
                'service_areas' => ['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan', 'Benin City', 'Warri', 'Asaba'],
                'contact_info' => [
                    'customer_service' => '121',
                    'website' => 'https://www.gloworld.com',
                    'email' => 'customercare@gloworld.com'
                ],
                'sort_order' => 3,
            ],
            [
                'name' => '9mobile',
                'code' => '9MOB',
                'full_name' => '9mobile Nigeria',
                'description' => 'Technology-driven telecommunications provider focused on digital innovation and customer experience.',
                'color' => '#00B04F',
                'type' => 'mobile',
                'is_active' => true,
                'coverage_percentage' => 75.2,
                'service_areas' => ['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan', 'Calabar', 'Uyo'],
                'contact_info' => [
                    'customer_service' => '200',
                    'website' => 'https://www.9mobile.com.ng',
                    'email' => 'care@9mobile.com.ng'
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Spectranet',
                'code' => 'SPEC',
                'full_name' => 'Spectranet Limited',
                'description' => 'Leading broadband internet service provider offering high-speed internet solutions.',
                'color' => '#0066CC',
                'type' => 'broadband',
                'is_active' => true,
                'coverage_percentage' => 45.8,
                'service_areas' => ['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan'],
                'contact_info' => [
                    'customer_service' => '+234-700-700-8001',
                    'website' => 'https://www.spectranet.com.ng',
                    'email' => 'customercare@spectranet.com.ng'
                ],
                'sort_order' => 5,
            ],
            [
                'name' => 'Smile',
                'code' => 'SMIL',
                'full_name' => 'Smile Communications Nigeria',
                'description' => '4G LTE broadband provider offering unlimited internet access across major Nigerian cities.',
                'color' => '#FF6600',
                'type' => 'broadband',
                'is_active' => true,
                'coverage_percentage' => 38.4,
                'service_areas' => ['Lagos', 'Abuja', 'Port Harcourt', 'Benin City', 'Ibadan'],
                'contact_info' => [
                    'customer_service' => '0700-765-4532',
                    'website' => 'https://www.smilecoms.com',
                    'email' => 'care@smilecoms.com'
                ],
                'sort_order' => 6,
            ],
        ];

        foreach ($networks as $network) {
            Network::firstOrCreate(['code' => $network['code']], $network);
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
