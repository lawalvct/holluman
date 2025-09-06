<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Http\Controllers\Admin\AdminController;

class TestN3tDataRetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:n3tdata-retry {--subscription-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test N3tdata retry functionality for a subscription';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptionId = $this->option('subscription-id');

        if (!$subscriptionId) {
            // Find a subscription with failed N3tdata status
            $subscription = Subscription::where('n3tdata_status', '!=', 'success')
                ->orWhereNull('n3tdata_status')
                ->first();

            if (!$subscription) {
                $this->info('No subscriptions found with failed/pending N3tdata status.');
                $this->info('Creating a test scenario...');

                $subscription = Subscription::first();
                if (!$subscription) {
                    $this->error('No subscriptions found in the database.');
                    return 1;
                }

                // Reset N3tdata status to simulate a failure
                $subscription->update([
                    'n3tdata_status' => 'failed',
                    'n3tdata_plan' => null,
                    'n3tdata_amount' => null,
                    'n3tdata_response' => ['message' => 'Test failure for retry']
                ]);
            }
        } else {
            $subscription = Subscription::find($subscriptionId);
            if (!$subscription) {
                $this->error("Subscription with ID {$subscriptionId} not found.");
                return 1;
            }
        }

        $this->info("Testing N3tdata retry for subscription ID: {$subscription->id}");
        $this->info("Current N3tdata status: " . ($subscription->n3tdata_status ?? 'null'));
        $this->info("User: {$subscription->user->name}");
        $this->info("Plan: {$subscription->subscriptionPlan->name}");
        $this->info("Phone: {$subscription->subscriber_phone}");

        $this->newLine();
        $this->info("Testing the retry method...");

        try {
            $adminController = new AdminController();
            $reflection = new \ReflectionClass($adminController);
            $method = $reflection->getMethod('activateDataSubscriptionForAdmin');
            $method->setAccessible(true);

            $result = $method->invoke($adminController, $subscription);

            $this->newLine();
            if ($result['success']) {
                $this->info("✅ N3tdata retry test successful!");
                $this->info("Message: " . $result['message']);
            } else {
                $this->error("❌ N3tdata retry test failed!");
                $this->error("Message: " . $result['message']);
            }

            // Refresh subscription to see updated data
            $subscription->refresh();
            $this->newLine();
            $this->info("Updated subscription data:");
            $this->info("N3tdata Status: " . ($subscription->n3tdata_status ?? 'null'));
            $this->info("N3tdata Plan: " . ($subscription->n3tdata_plan ?? 'null'));
            $this->info("N3tdata Amount: " . ($subscription->n3tdata_amount ?? 'null'));
            $this->info("Data Activated At: " . ($subscription->data_activated_at ?? 'null'));

        } catch (\Exception $e) {
            $this->error("Exception during test: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
