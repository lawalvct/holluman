<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@isp.com',
            'phone' => '+234801234567',
            'address' => 'Admin Office, Lagos, Nigeria',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create wallet for admin (optional)
        Wallet::create([
            'user_id' => $admin->id,
            'balance' => 0.00,
        ]);

        // Create sample subscription plans
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Perfect for light internet usage. Suitable for browsing and basic downloads.',
                'price' => 5000.00,
                'duration_days' => 30,
                'speed' => '10 Mbps',
                'data_limit' => '50 GB',
                'features' => ['24/7 Support', 'Basic Speed', 'Email Support'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard Plan',
                'description' => 'Great for streaming and gaming. Perfect for small families.',
                'price' => 10000.00,
                'duration_days' => 30,
                'speed' => '25 Mbps',
                'data_limit' => '150 GB',
                'features' => ['24/7 Support', 'Standard Speed', 'Phone Support', 'HD Streaming'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'High-speed internet for power users. Unlimited downloads and streaming.',
                'price' => 20000.00,
                'duration_days' => 30,
                'speed' => '50 Mbps',
                'data_limit' => 'Unlimited',
                'features' => ['24/7 Support', 'High Speed', 'Priority Support', '4K Streaming', 'Gaming Optimized'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business Plan',
                'description' => 'Enterprise-grade internet for businesses and teams.',
                'price' => 50000.00,
                'duration_days' => 30,
                'speed' => '100 Mbps',
                'data_limit' => 'Unlimited',
                'features' => ['24/7 Support', 'Enterprise Speed', 'Dedicated Support', 'Static IP', 'SLA Guarantee'],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        // Create a test user
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'phone' => '+234809876543',
            'address' => 'Test Address, Abuja, Nigeria',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create wallet for test user
        Wallet::create([
            'user_id' => $testUser->id,
            'balance' => 15000.00, // Some initial balance for testing
        ]);
    }
}
