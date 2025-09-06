<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class CheckSubscriptionStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:subscription-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription statuses and N3tdata eligibility';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Subscription Status Report');
        $this->newLine();

        $subscriptions = Subscription::with(['user', 'subscriptionPlan'])->get();

        if ($subscriptions->isEmpty()) {
            $this->warn('No subscriptions found in the database.');
            return 0;
        }

        $this->table(
            ['ID', 'User', 'Plan', 'Status', 'N3tdata Status', 'Retry Eligible'],
            $subscriptions->map(function ($subscription) {
                $retryEligible = (
                    $subscription->n3tdata_status !== 'success' &&
                    in_array($subscription->status, ['active', 'expired'])
                ) ? '✅ Yes' : '❌ No';

                $reason = '';
                if ($subscription->n3tdata_status === 'success') {
                    $reason = '(Already successful)';
                } elseif (!in_array($subscription->status, ['active', 'expired'])) {
                    $reason = '(Payment not successful)';
                }

                return [
                    $subscription->id,
                    $subscription->user->name ?? 'N/A',
                    $subscription->subscriptionPlan->name ?? 'N/A',
                    $subscription->status ?? 'N/A',
                    $subscription->n3tdata_status ?? 'null',
                    $retryEligible . ' ' . $reason
                ];
            })->toArray()
        );

        $this->newLine();
        $totalSubscriptions = $subscriptions->count();
        $eligibleForRetry = $subscriptions->filter(function ($subscription) {
            return $subscription->n3tdata_status !== 'success' &&
                   in_array($subscription->status, ['active', 'expired']);
        })->count();

        $this->info("📈 Summary:");
        $this->info("• Total Subscriptions: {$totalSubscriptions}");
        $this->info("• Eligible for N3tdata Retry: {$eligibleForRetry}");

        return 0;
    }
}
