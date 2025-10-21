<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\N3tdataPlan;
use Illuminate\Support\Facades\DB;

class N3tdataPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        N3tdataPlan::truncate();

        $plans = [
            // MTN Plans (network_id = 1)
            ['id' => 2, 'network_id' => 1, 'plan_type' => 'SME', 'plan_name' => '1GB', 'amount' => 650.00, 'duration' => '1 Month'],
            ['id' => 3, 'network_id' => 1, 'plan_type' => 'SME', 'plan_name' => '2GB', 'amount' => 1300.00, 'duration' => '1 Month'],
            ['id' => 4, 'network_id' => 1, 'plan_type' => 'SME', 'plan_name' => '3GB', 'amount' => 1950.00, 'duration' => '1 Month'],
            ['id' => 5, 'network_id' => 1, 'plan_type' => 'SME', 'plan_name' => '5GB', 'amount' => 3250.00, 'duration' => '1 Month'],
            ['id' => 6, 'network_id' => 1, 'plan_type' => 'SME', 'plan_name' => '10GB', 'amount' => 5000.00, 'duration' => '1 Month'],
            ['id' => 50, 'network_id' => 1, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '500MB', 'amount' => 400.00, 'duration' => '1 Month'],
            ['id' => 51, 'network_id' => 1, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '1GB', 'amount' => 700.00, 'duration' => '1 Month Datashare'],
            ['id' => 52, 'network_id' => 1, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '2GB', 'amount' => 1300.00, 'duration' => '1 Month Datashare'],
            ['id' => 53, 'network_id' => 1, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '3GB', 'amount' => 1800.00, 'duration' => '1 Month Datashare'],
            ['id' => 54, 'network_id' => 1, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '5GB', 'amount' => 3000.00, 'duration' => '1 Month Datashare'],
            ['id' => 101, 'network_id' => 1, 'plan_type' => 'GIFTING', 'plan_name' => '1GB', 'amount' => 520.00, 'duration' => '1Day Awoof Data'],
            ['id' => 102, 'network_id' => 1, 'plan_type' => 'GIFTING', 'plan_name' => '1.5GB', 'amount' => 600.00, 'duration' => '2Day Awoof Data'],
            ['id' => 105, 'network_id' => 1, 'plan_type' => 'GIFTING', 'plan_name' => '2GB', 'amount' => 800.00, 'duration' => '2Day Awoof Data'],
            ['id' => 107, 'network_id' => 1, 'plan_type' => 'GIFTING', 'plan_name' => '750MB', 'amount' => 450.00, 'duration' => '3days'],
            ['id' => 108, 'network_id' => 1, 'plan_type' => 'GIFTING', 'plan_name' => '1GB', 'amount' => 800.00, 'duration' => '7 days'],
            ['id' => 109, 'network_id' => 1, 'plan_type' => 'GIFTING', 'plan_name' => '2GB', 'amount' => 1550.00, 'duration' => '1 Month'],

            // AIRTEL Plans (network_id = 2)
            ['id' => 46, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '4GB', 'amount' => 2500.00, 'duration' => '1 Month'],
            ['id' => 47, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '3GB', 'amount' => 2000.00, 'duration' => '1 Month'],
            ['id' => 48, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '2GB', 'amount' => 1500.00, 'duration' => '1 Month'],
            ['id' => 49, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '1GB', 'amount' => 800.00, 'duration' => '7Days'],
            ['id' => 56, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '500MB', 'amount' => 500.00, 'duration' => '1 Month'],
            ['id' => 70, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '300MB', 'amount' => 300.00, 'duration' => '1Month'],
            ['id' => 71, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '100MB', 'amount' => 100.00, 'duration' => '1Month'],
            ['id' => 106, 'network_id' => 2, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '10GB', 'amount' => 4000.00, 'duration' => '1 Month'],
            ['id' => 85, 'network_id' => 2, 'plan_type' => 'GIFTING', 'plan_name' => '1GB', 'amount' => 450.00, 'duration' => '3Days Awoof Data'],
            ['id' => 86, 'network_id' => 2, 'plan_type' => 'GIFTING', 'plan_name' => '2GB', 'amount' => 650.00, 'duration' => '2Days Awoof Data'],
            ['id' => 87, 'network_id' => 2, 'plan_type' => 'GIFTING', 'plan_name' => '3GB', 'amount' => 1150.00, 'duration' => '2days Awoof Data'],
            ['id' => 88, 'network_id' => 2, 'plan_type' => 'GIFTING', 'plan_name' => '7GB', 'amount' => 2150.00, 'duration' => '1Month Awoof Data'],
            ['id' => 89, 'network_id' => 2, 'plan_type' => 'GIFTING', 'plan_name' => '10GB', 'amount' => 3200.00, 'duration' => '1Month Awoof Data'],

            // GLO Plans (network_id = 3)
            ['id' => 57, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '200MB', 'amount' => 100.00, 'duration' => '1 Month'],
            ['id' => 58, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '500MB', 'amount' => 215.00, 'duration' => '1 Month'],
            ['id' => 59, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '1GB', 'amount' => 430.00, 'duration' => '1 Month'],
            ['id' => 60, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '2GB', 'amount' => 860.00, 'duration' => '1 Month'],
            ['id' => 61, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '3GB', 'amount' => 1290.00, 'duration' => '1 Month'],
            ['id' => 62, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '5GB', 'amount' => 2150.00, 'duration' => '1 Month'],
            ['id' => 63, 'network_id' => 3, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '10GB', 'amount' => 4300.00, 'duration' => '1 Month'],
            ['id' => 90, 'network_id' => 3, 'plan_type' => 'GIFTING', 'plan_name' => '750MB', 'amount' => 220.00, 'duration' => '1Day Awoof Data'],
            ['id' => 91, 'network_id' => 3, 'plan_type' => 'GIFTING', 'plan_name' => '1.5GB', 'amount' => 320.00, 'duration' => '1Day Awoof Data'],
            ['id' => 92, 'network_id' => 3, 'plan_type' => 'GIFTING', 'plan_name' => '2.5GB', 'amount' => 500.00, 'duration' => '2Days Awoof Data'],
            ['id' => 93, 'network_id' => 3, 'plan_type' => 'GIFTING', 'plan_name' => '10GB', 'amount' => 2000.00, 'duration' => '7Days Awoof Data'],

            // 9MOBILE Plans (network_id = 4)
            ['id' => 36, 'network_id' => 4, 'plan_type' => 'GIFTING', 'plan_name' => '1.5GB', 'amount' => 880.00, 'duration' => '1 Month'],
            ['id' => 37, 'network_id' => 4, 'plan_type' => 'GIFTING', 'plan_name' => '2GB', 'amount' => 1000.00, 'duration' => '1 Month'],
            ['id' => 38, 'network_id' => 4, 'plan_type' => 'GIFTING', 'plan_name' => '3GB', 'amount' => 1300.00, 'duration' => '1 Month'],
            ['id' => 39, 'network_id' => 4, 'plan_type' => 'GIFTING', 'plan_name' => '4.5GB', 'amount' => 1750.00, 'duration' => '1 Month'],
            ['id' => 72, 'network_id' => 4, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '500MB', 'amount' => 90.00, 'duration' => '1Month'],
            ['id' => 73, 'network_id' => 4, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '1GB', 'amount' => 170.00, 'duration' => '1Month'],
            ['id' => 74, 'network_id' => 4, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '2GB', 'amount' => 340.00, 'duration' => '1Month'],
            ['id' => 75, 'network_id' => 4, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '3GB', 'amount' => 510.00, 'duration' => '1Month'],
            ['id' => 76, 'network_id' => 4, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '5GB', 'amount' => 850.00, 'duration' => '1Month'],
            ['id' => 77, 'network_id' => 4, 'plan_type' => 'COOPERATE GIFTING', 'plan_name' => '10GB', 'amount' => 1700.00, 'duration' => '1Month'],
        ];

        foreach ($plans as $plan) {
            N3tdataPlan::create($plan);
        }

        $this->command->info('N3tdata plans seeded successfully!');
    }
}
