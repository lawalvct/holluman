<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionStatusService;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    protected $subscriptionStatusService;

    public function __construct(SubscriptionStatusService $subscriptionStatusService)
    {
        $this->subscriptionStatusService = $subscriptionStatusService;
    }

    /**
     * Update all expired subscriptions
     * This endpoint should be called by cron-job.org or similar service
     *
     * URL: https://yourdomain.com/cron/update-subscriptions?token=YOUR_SECRET_TOKEN
     * Recommended: Run every 1 hour
     */
    public function updateSubscriptions(Request $request)
    {
        // Validate cron token for security
        $cronToken = config('app.cron_token');
        $requestToken = $request->query('token') ?? $request->header('X-Cron-Token');

        if (!$cronToken || $requestToken !== $cronToken) {
            Log::warning('Unauthorized cron job attempt', [
                'ip' => $request->ip(),
                'token_provided' => !empty($requestToken)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

        // Run the subscription update
        $result = $this->subscriptionStatusService->updateAllExpiredSubscriptions();

        // Log the cron execution
        Log::info('Cron job executed: update-subscriptions', [
            'ip' => $request->ip(),
            'result' => $result
        ]);

        return response()->json($result);
    }

    /**
     * Get subscription statistics
     * This is a helpful endpoint to monitor your subscriptions
     *
     * URL: https://yourdomain.com/cron/subscription-stats?token=YOUR_SECRET_TOKEN
     */
    public function getSubscriptionStats(Request $request)
    {
        // Validate cron token
        $cronToken = config('app.cron_token');
        $requestToken = $request->query('token') ?? $request->header('X-Cron-Token');

        if (!$cronToken || $requestToken !== $cronToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

        $stats = $this->subscriptionStatusService->getSubscriptionStats();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Health check endpoint
     * No authentication required - use this to verify your cron is working
     *
     * URL: https://yourdomain.com/cron/health
     */
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'Veesta Cron Service',
            'timestamp' => now()->toDateTimeString(),
            'message' => 'Cron endpoint is accessible and working'
        ]);
    }
}
