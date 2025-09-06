<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestWalletCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:wallet-credit {--user-id=1} {--amount=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test wallet credit functionality for admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $amount = $this->option('amount');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        $this->info("Testing wallet credit functionality...");
        $this->info("User: {$user->name} (ID: {$user->id})");
        $this->info("Balance before: {$user->wallet->formatted_balance}");

        try {
            $transaction = $user->wallet->credit(
                $amount,
                "Test credit of ₦{$amount} by admin (CLI test)",
                ['admin_test' => true, 'test_time' => now()]
            );

            $this->info("✅ Credit successful!");
            $this->info("Balance after: {$user->wallet->formatted_balance}");
            $this->info("Transaction ID: {$transaction->id}");
            $this->info("Transaction Reference: {$transaction->reference}");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Credit failed: " . $e->getMessage());
            return 1;
        }
    }
}
