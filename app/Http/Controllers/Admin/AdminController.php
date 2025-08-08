<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Payment;
use App\Models\WalletTransaction;
use App\Models\Network;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        // Get statistics
        $totalUsers = User::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $totalRevenue = Payment::where('status', 'successful')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'successful')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Get recent data (with error handling)
        try {
            $recentSubscriptions = Subscription::with(['user', 'subscriptionPlan'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $recentSubscriptions = collect();
        }

        try {
            $recentPayments = Payment::with('user')
                ->latest()
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            $recentPayments = collect();
        }

        try {
            $topPlans = SubscriptionPlan::withCount('subscriptions')
                ->orderBy('subscriptions_count', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $topPlans = collect();
        }

        return view('dashboard.admin', compact(
            'totalUsers',
            'activeSubscriptions',
            'totalRevenue',
            'monthlyRevenue',
            'recentSubscriptions',
            'recentPayments',
            'topPlans'
        ));
    }

    /**
     * Display users management
     */
    public function users(Request $request)
    {
        $query = User::where('role', 'user')
            ->with(['wallet', 'subscriptions.subscriptionPlan'])
            ->withCount(['subscriptions']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by subscription status
        if ($request->filled('subscription_status')) {
            if ($request->subscription_status === 'active') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active')->where('end_date', '>', now());
                });
            } elseif ($request->subscription_status === 'expired') {
                $query->whereDoesntHave('subscriptions', function($q) {
                    $q->where('status', 'active')->where('end_date', '>', now());
                });
            }
        }

        $users = $query->latest()->paginate(15);

        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'users_with_active_subscriptions' => User::whereHas('subscriptions', function($q) {
                $q->where('status', 'active')->where('end_date', '>', now());
            })->count(),
            'total_wallet_balance' => User::where('role', 'user')->with('wallet')->get()->sum('wallet.balance'),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load([
            'wallet',
            'subscriptions.subscriptionPlan',
            'walletTransactions' => function($query) {
                $query->latest()->take(10);
            }
        ]);

        $activeSubscription = $user->activeSubscription();

        $subscriptionHistory = $user->subscriptions()
            ->with('subscriptionPlan')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_spent' => $user->subscriptions()->sum('amount_paid'),
            'wallet_balance' => $user->wallet->balance,
            'total_subscriptions' => $user->subscriptions()->count(),
            'active_subscription' => $activeSubscription ? 1 : 0,
        ];

        return view('admin.users.show', compact('user', 'stats', 'subscriptionHistory', 'activeSubscription'));
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $user->update([
            'is_active' => $request->is_active
        ]);

        $status = $request->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User has been {$status} successfully.");
    }

    /**
     * Credit user wallet
     */
    public function creditWallet(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $user->wallet->credit(
                $request->amount,
                $request->description,
                ['admin_id' => auth()->id(), 'admin_name' => auth()->user()->name]
            );

            DB::commit();
            return back()->with('success', 'Wallet credited successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to credit wallet. Please try again.');
        }
    }

    /**
     * Debit user wallet
     */
    public function debitWallet(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $user->wallet->balance,
            'description' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $user->wallet->debit(
                $request->amount,
                $request->description,
                ['admin_id' => auth()->id(), 'admin_name' => auth()->user()->name]
            );

            DB::commit();
            return back()->with('success', 'Wallet debited successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to debit wallet. ' . $e->getMessage());
        }
    }

    /**
     * Display subscription plans management
     */
    public function plans()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->with(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_plans' => SubscriptionPlan::where('is_active', true)->count(),
            'total_subscriptions' => Subscription::count(),
            'total_revenue' => Subscription::sum('amount_paid'),
        ];

        return view('admin.plans.index', compact('plans', 'stats'));
    }

    /**
     * Display subscriptions management
     */
    public function subscriptions(Request $request)
    {
        $query = Subscription::with(['user', 'subscriptionPlan']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        // Search by user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(15);
        $plans = SubscriptionPlan::all();

        $stats = [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'expired_subscriptions' => Subscription::where('status', 'expired')->count(),
            'cancelled_subscriptions' => Subscription::where('status', 'cancelled')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    /**
     * Display payments management
     */
    public function payments(Request $request)
    {
        $query = Payment::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by user or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(15);

        $stats = [
            'total_payments' => Payment::count(),
            'successful_payments' => Payment::where('status', 'completed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display reports
     */
    public function reports()
    {
        // Revenue data for the last 12 months
        $monthlyRevenue = Payment::where('status', 'completed')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // User registration data for the last 12 months
        $monthlyRegistrations = User::where('role', 'user')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Plan popularity
        $planPopularity = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('subscriptions_count', 'desc')
            ->get();

        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'total_users' => User::where('role', 'user')->count(),
            'monthly_users' => User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('admin.reports.index', compact('monthlyRevenue', 'monthlyRegistrations', 'planPopularity', 'stats'));
    }

    /**
     * Display settings
     */
    public function settings()
    {
        return view('admin.settings.index');
    }

    /**
     * Display networks management
     */
    public function networks(Request $request)
    {
        $query = Network::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $networks = $query->ordered()->paginate(15);

        $stats = [
            'total_networks' => Network::count(),
            'active_networks' => Network::where('is_active', true)->count(),
            'mobile_networks' => Network::where('type', 'mobile')->count(),
            'broadband_networks' => Network::where('type', 'broadband')->count(),
        ];

        return view('admin.networks.index', compact('networks', 'stats'));
    }

    /**
     * Show network creation form
     */
    public function createNetwork()
    {
        return view('admin.networks.create');
    }

    /**
     * Store new network
     */
    public function storeNetwork(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:networks,code',
            'full_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7',
            'type' => 'nullable|in:mobile,broadband,fiber,satellite',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'service_areas' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('networks', 'public');
            $data['image'] = $imagePath;
        }

        Network::create($data);

        return redirect()->route('admin.networks')->with('success', 'Network created successfully.');
    }

    /**
     * Show network details
     */
    public function showNetwork(Network $network)
    {
        return view('admin.networks.show', compact('network'));
    }

    /**
     * Show network edit form
     */
    public function editNetwork(Network $network)
    {
        return view('admin.networks.edit', compact('network'));
    }

    /**
     * Update network
     */
    public function updateNetwork(Request $request, Network $network)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:networks,code,' . $network->id,
            'full_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7',
            'type' => 'required|in:mobile,broadband,fiber,satellite',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'service_areas' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($network->image) {
                Storage::disk('public')->delete($network->image);
            }
            $imagePath = $request->file('image')->store('networks', 'public');
            $data['image'] = $imagePath;
        }

        $network->update($data);

        return redirect()->route('admin.networks')->with('success', 'Network updated successfully.');
    }

    /**
     * Toggle network status
     */
    public function toggleNetworkStatus(Network $network)
    {
        $network->update([
            'is_active' => !$network->is_active
        ]);

        $status = $network->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Network has been {$status} successfully.");
    }

    /**
     * Delete network
     */
    public function destroyNetwork(Network $network)
    {
        // Delete image if exists
        if ($network->image) {
            Storage::disk('public')->delete($network->image);
        }

        $network->delete();

        return redirect()->route('admin.networks')->with('success', 'Network deleted successfully.');
    }
}
