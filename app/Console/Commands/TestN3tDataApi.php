<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\N3tDataHelper;

class TestN3tDataApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:n3tdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test N3tdata API connection and access token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing N3tdata API...');
        $this->newLine();

        $helper = new N3tDataHelper();

        // Test 1: Get Access Token
        $this->info('1. Testing getAccessToken()...');
        $tokenResult = $helper->getAccessToken();

        if ($tokenResult['success']) {
            $this->info('✅ Access Token: SUCCESS');
            $this->info('   Token: ' . substr($tokenResult['access_token'], 0, 20) . '...');
            $this->info('   Username: ' . $tokenResult['username']);
            $this->info('   Balance: ' . $tokenResult['balance']);
        } else {
            $this->error('❌ Access Token: FAILED');
            $this->error('   Error: ' . $tokenResult['message']);
        }

        $this->newLine();

        // Test 2: Get Balance
        $this->info('2. Testing getBalance()...');
        $balanceResult = $helper->getBalance();

        if ($balanceResult['success']) {
            $this->info('✅ Balance: SUCCESS');
            $this->info('   Balance: ' . $balanceResult['balance']);
        } else {
            $this->error('❌ Balance: FAILED');
            $this->error('   Error: ' . $balanceResult['message']);
        }

        $this->newLine();

        // Show configuration
        $this->info('Configuration:');
        $this->info('   Username from env: ' . env('N3TDATA_USERNAME', 'Not set'));
        $this->info('   Password from env: ' . (env('N3TDATA_PASSWORD') ? 'Set (' . strlen(env('N3TDATA_PASSWORD')) . ' chars)' : 'Not set'));

        // Test direct credentials
        $this->newLine();
        $this->info('3. Testing with hardcoded credentials...');

        $username = 'holluman';
        $password = 'Hollumidey19@';
        $auth = base64_encode($username . ':' . $password);

        $this->info('   Testing username: ' . $username);
        $this->info('   Password length: ' . strlen($password));
        $this->info('   Basic Auth: ' . substr($auth, 0, 20) . '...');

        // Test the API directly with credentials
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
            'Content-Type' => 'application/json'
        ])->post('https://n3tdata.com/api/user');

        $result = $response->json();

        $this->info('   HTTP Status: ' . $response->status());
        $this->info('   Response: ' . json_encode($result, JSON_PRETTY_PRINT));

        // Also test without Content-Type header (in case it's causing issues)
        $this->newLine();
        $this->info('4. Testing without Content-Type header...');

        $response2 = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
        ])->post('https://n3tdata.com/api/user');

        $result2 = $response2->json();

        $this->info('   HTTP Status: ' . $response2->status());
        $this->info('   Response: ' . json_encode($result2, JSON_PRETTY_PRINT));

        // Test plan mapping
        $this->newLine();
        $this->info('6. Testing plan mapping...');

        // Get a subscription plan to test mapping
        $plans = \App\Models\SubscriptionPlan::active()->take(3)->get();

        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $mappedId = $helper->mapDataPlanId($plan);
                $this->info("   Plan: {$plan->name} (ID: {$plan->id}) -> N3tdata Plan ID: {$mappedId} (plainid: {$plan->plainid})");
            }
        } else {
            $this->warn('   No subscription plans found in database');
        }

        return 0;
    }
}
