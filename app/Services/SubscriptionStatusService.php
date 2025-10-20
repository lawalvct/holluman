<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionStatusService
{
    /**
     * Update expired subscriptions for a specific user
     * This is called when user accesses their dashboard
     */
    public function updateUserSubscriptionStatuses($userId)
    {
        $cacheKey = "subscription_check_user_{$userId}";

        // Check cache - only run this once every 10 minutes per user
        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            // Find user's active subscriptions that have actually expired
            $expiredSubscriptions = Subscription::where('user_id', $userId)
                ->where('status', 'active')
                ->where('end_date', '<', now())
                ->get();

            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => 'expired']);

                Log::info('Subscription auto-expired', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $userId,
                    'end_date' => $subscription->end_date
                ]);
            }

            // Cache for 10 minutes to prevent repeated checks
            Cache::put($cacheKey, true, now()->addMinutes(10));

        } catch (\Exception $e) {
            Log::error('Error updating user subscription statuses', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update all expired subscriptions
     * This is called via cron endpoint (should run once per hour)
     */
    public function updateAllExpiredSubscriptions()
    {
        $cacheKey = 'subscription_check_all';

        // Check cache - only run this once per hour globally
        if (Cache::has($cacheKey)) {
            return [
                'success' => false,
                'message' => 'Subscription check already ran recently. Try again later.',
                'updated' => 0
            ];
        }

        try {
            // Find all active subscriptions that have expired
            $expiredSubscriptions = Subscription::where('status', 'active')
                ->where('end_date', '<', now())
                ->get();

            $count = 0;
            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => 'expired']);
                $count++;
            }

            // Cache for 1 hour
            Cache::put($cacheKey, true, now()->addHour());

            Log::info('Bulk subscription expiry check completed', [
                'expired_count' => $count,
                'timestamp' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Subscription statuses updated successfully',
                'updated' => $count,
                'timestamp' => now()->toDateTimeString()
            ];

        } catch (\Exception $e) {
            Log::error('Error in bulk subscription status update', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error updating subscriptions: ' . $e->getMessage(),
                'updated' => 0
            ];
        }
    }

    /**
     * Get statistics about subscriptions
     */
    public function getSubscriptionStats()
    {
        return [
            'total_active' => Subscription::where('status', 'active')
                ->where('end_date', '>', now())
                ->count(),
            'expired_needing_update' => Subscription::where('status', 'active')
                ->where('end_date', '<', now())
                ->count(),
            'total_expired' => Subscription::where('status', 'expired')->count(),
            'expiring_soon' => Subscription::where('status', 'active')
                ->where('end_date', '>', now())
                ->where('end_date', '<', now()->addDays(3))
                ->count(),
        ];
    }
}
