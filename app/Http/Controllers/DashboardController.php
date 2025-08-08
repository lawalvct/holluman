<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * User dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        // Get user's active subscription
        $activeSubscription = $user->activeSubscription();

        // Get recent transactions
        $recentTransactions = $user->walletTransactions()
            ->latest()
            ->take(5)
            ->get();

        // Get subscription history
        $recentSubscriptions = $user->subscriptions()
            ->with('subscriptionPlan')
            ->latest()
            ->take(3)
            ->get();

        // Get available plans for quick subscription
        $featuredPlans = SubscriptionPlan::active()
            ->ordered()
            ->take(3)
            ->get();

        return view('dashboard.user', compact(
            'user',
            'wallet',
            'activeSubscription',
            'recentTransactions',
            'recentSubscriptions',
            'featuredPlans'
        ));
    }

    /**
     * Admin dashboard
     */
    public function admin()
    {
        // Dashboard statistics
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::active()->count(),
            'total_revenue' => Payment::successful()->sum('amount'),
            'monthly_revenue' => Payment::successful()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Recent activities
        $recentSubscriptions = Subscription::with(['user', 'subscriptionPlan'])
            ->latest()
            ->take(10)
            ->get();

        $recentPayments = Payment::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Top plans
        $topPlans = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('subscriptions_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'recentSubscriptions',
            'recentPayments',
            'topPlans'
        ));
    }
}
