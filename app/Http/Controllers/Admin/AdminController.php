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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display all user SIMs for admin management
     */
    public function sims(Request $request)
    {
        $query = \App\Models\Sim::with('user');

        // Search by sim_number or camera_name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sim_number', 'like', "%$search%")
                  ->orWhere('camera_name', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%")
                         ->orWhere('id', $search);
                  });
            });
        }

        $sims = $query->latest()->paginate(15);
        $stats = [
            'total_sims' => \App\Models\Sim::count(),
            'unique_users' => \App\Models\Sim::distinct('user_id')->count('user_id'),
        ];
        return view('admin.sims.index', compact('sims', 'stats'));
    }

    /**
     * Show a single SIM details
     */
    public function showSim(\App\Models\Sim $sim)
    {
        $sim->load('user');
        return view('admin.sims.show', compact('sim'));
    }

    /**
     * Show SIM edit form
     */
    public function editSim(\App\Models\Sim $sim)
    {
        $sim->load('user');
        return view('admin.sims.edit', compact('sim'));
    }

    /**
     * Update SIM details
     */
    public function updateSim(Request $request, \App\Models\Sim $sim)
    {
        $request->validate([
            'sim_number' => 'required|string|unique:sims,sim_number,' . $sim->id,
            'camera_name' => 'required|string|max:255',
            'camera_location' => 'required|string|max:255',
        ]);
        $sim->update($request->only(['sim_number', 'camera_name', 'camera_location']));
        return redirect()->route('admin.sims')->with('success', 'SIM updated successfully.');
    }

    /**
     * Delete a SIM
     */
    public function destroySim(\App\Models\Sim $sim)
    {
        $sim->delete();
        return redirect()->route('admin.sims')->with('success', 'SIM deleted successfully.');
    }
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
            $n3tdataBalance = $this->getN3tdataBalance();

        return view('dashboard.admin', compact(
            'totalUsers',
            'activeSubscriptions',
            'totalRevenue',
            'monthlyRevenue',
            'recentSubscriptions',
            'recentPayments',
            'topPlans',
               'n3tdataBalance'
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
     * Display and update application settings
     */
    public function settings(Request $request)
    {
        $settings = \App\Models\Setting::getAppSettings();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_address' => 'nullable|string|max:255',
                'company_email' => 'nullable|email|max:255',
                'nomba_account_id' => 'nullable|string|max:255',
                'nomba_client_id' => 'nullable|string|max:255',
                'nomba_private_key' => 'nullable|string|max:255',
                'nomba_webhook_secret' => 'nullable|string|max:255',
                'paystack_public_key' => 'nullable|string|max:255',
                'paystack_secret_key' => 'nullable|string|max:255',
                'paystack_enabled' => 'nullable|boolean',
                'support_phone' => 'nullable|string|max:255',
                'support_email' => 'nullable|email|max:255',
            ]);

            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                $logo = $request->file('company_logo');
                $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('images'), $logoName);
                \App\Models\Setting::setValue('company_logo', $logoName, 'Company Logo');
            }

            // Save all other settings
            $fields = [
                'company_name', 'company_address', 'company_email',
                'nomba_account_id', 'nomba_client_id', 'nomba_private_key', 'nomba_webhook_secret',
                'paystack_public_key', 'paystack_secret_key', 'support_phone', 'support_email'
            ];
            foreach ($fields as $field) {
                \App\Models\Setting::setValue($field, $request->input($field));
            }
            \App\Models\Setting::setValue('paystack_enabled', $request->has('paystack_enabled') ? 1 : 0);

            return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
        }

        // Prepare settings for form
        $formSettings = [
            'company_name' => $settings['company_name']->value ?? '',
            'company_logo' => $settings['company_logo']->value ?? '',
            'company_address' => $settings['company_address']->value ?? '',
            'company_email' => $settings['company_email']->value ?? '',
            'nomba_account_id' => $settings['nomba_account_id']->value ?? '',
            'nomba_client_id' => $settings['nomba_client_id']->value ?? '',
            'nomba_private_key' => $settings['nomba_private_key']->value ?? '',
            'nomba_webhook_secret' => $settings['nomba_webhook_secret']->value ?? '',
            'paystack_public_key' => $settings['paystack_public_key']->value ?? '',
            'paystack_secret_key' => $settings['paystack_secret_key']->value ?? '',
            'paystack_enabled' => $settings['paystack_enabled']->value ?? 0,
            'support_phone' => $settings['support_phone']->value ?? '',
            'support_email' => $settings['support_email']->value ?? '',
        ];

        return view('admin.settings.index', ['settings' => $formSettings]);
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

    /**
     * Show a single subscription
     */
    public function showSubscription(Subscription $subscription)
    {
        $subscription->load(['user', 'subscriptionPlan']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Update subscription status
     */
    public function updateSubscriptionStatus(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status' => 'required|in:active,expired,cancelled'
        ]);
        $subscription->update(['status' => $request->status]);
        return back()->with('success', 'Subscription status updated.');
    }

    /**
     * Delete a subscription
     */
    public function destroySubscription(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions')->with('success', 'Subscription deleted.');
    }

    /**
     * Retry N3tdata activation for a subscription
     */
    public function retryN3tDataActivation(Subscription $subscription)
    {
        DB::beginTransaction();
        try {
            // Check if subscription is eligible for retry
            if ($subscription->n3tdata_status === 'success') {
                return back()->with('warning', 'N3tdata activation was already successful for this subscription.');
            }

            // Check if subscription payment was successful
            if (!in_array($subscription->status, ['active', 'expired'])) {
                return back()->with('error', 'Cannot retry N3tdata activation. Subscription payment must be successful first. Current status: ' . $subscription->status);
            }

            // Reset N3tdata fields before retry
            $subscription->update([
                'n3tdata_status' => null,
                'n3tdata_plan' => null,
                'n3tdata_amount' => null,
                'n3tdata_phone_number' => null,
                'n3tdata_request_id' => null,
                'n3tdata_response' => null,
                'data_activated_at' => null,
            ]);

            // Activate data subscription using the same method from SubscriptionController


            $dataActivationResult = $this->activateDataSubscriptionForAdmin($subscription);

            DB::commit();

            if ($dataActivationResult['success']) {
                Log::info('N3tdata retry successful', [
                    'subscription_id' => $subscription->id,
                    'admin_id' => auth()->id()
                ]);

                return back()->with('success', 'N3tdata activation retry successful! ' . $dataActivationResult['message']);
            } else {
                return back()->with('error', 'N3tdata activation retry failed: ' . $dataActivationResult['message']);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('N3tdata retry exception', [
                'subscription_id' => $subscription->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'An error occurred while retrying N3tdata activation: ' . $e->getMessage());
        }
    }

    /**
     * Activate data subscription using N3tdata API (Admin version)
     */
    private function activateDataSubscriptionForAdmin($subscription)
    {
        try {
            $n3tDataHelper = new \App\Helpers\N3tDataHelper();

            // Use the direct network ID (not mapped)
            $n3tNetworkId = $subscription->network_id;

            // Map subscription plan to N3tdata data plan ID using network's n3tdata_plainid
            $dataPlanId = $n3tDataHelper->mapDataPlanId($subscription->subscriptionPlan, $subscription->network_id);

            // Generate unique request ID for retry
            $requestId = 'RETRY_' . $subscription->id . '_' . time();

            // Log what we're sending to N3tdata
            Log::info('Sending data to N3tdata API (Admin Retry)', [
                'subscription_id' => $subscription->id,
                'n3tNetworkId' => $n3tNetworkId,
                'subscriber_phone' => $subscription->subscriber_phone,
                'dataPlanId' => $dataPlanId,
                'requestId' => $requestId,
                'network_name' => $subscription->network->name ?? 'Unknown',
                'plan_name' => $subscription->subscriptionPlan->name ?? 'Unknown',
                'amount_paid' => $subscription->amount_paid,
            ]);

            // Purchase data subscription
            $result = $n3tDataHelper->purchaseDataSubscription(
                $n3tNetworkId,
                $subscription->subscriber_phone,
                $dataPlanId,
                $requestId
            );

            if ($result['success']) {
                // Parse N3tdata response for display columns
                $responseData = $result['data'] ?? [];

                // Update subscription with N3tdata response and parsed data
                $subscription->update([
                    'n3tdata_status' => 'success',
                    'n3tdata_plan' => $responseData['dataplan'] ?? null,
                    'n3tdata_amount' => $responseData['amount'] ?? $subscription->amount_paid,
                    'n3tdata_phone_number' => $responseData['phone_number'] ?? $subscription->subscriber_phone,
                    'n3tdata_request_id' => $requestId,
                    'n3tdata_response' => $responseData,
                    'data_activated_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Data activation completed successfully'
                ];
            } else {
                // Parse failed response data
                $responseData = $result['data'] ?? [];

                // Update subscription with failure info and parsed data
                $subscription->update([
                    'n3tdata_status' => 'failed',
                    'n3tdata_plan' => $responseData['dataplan'] ?? null,
                    'n3tdata_amount' => $responseData['amount'] ?? null,
                    'n3tdata_phone_number' => $subscription->subscriber_phone,
                    'n3tdata_request_id' => $requestId,
                    'n3tdata_response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Data activation failed'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception during admin data subscription retry', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Technical error during data activation: ' . $e->getMessage()
            ];
        }
    }

     //GET N3tdata balance
    public function getN3tdataBalance()
    {
        try {
            $n3tDataHelper = new \App\Helpers\N3tDataHelper();
            $balanceResult = $n3tDataHelper->getBalance();

            if ($balanceResult['success']) {
                // Convert string balance to float (remove commas and convert)
                $balance = $balanceResult['balance'];

                // Remove commas and convert to float
                if (is_string($balance)) {
                    $balance = str_replace(',', '', $balance);
                }

                $balanceFloat = (float) $balance;

                // Return JSON response for AJAX requests
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'balance' => $balanceFloat,
                        'formatted_balance' => '₦' . number_format($balanceFloat, 2),
                        'timestamp' => now()->toISOString()
                    ]);
                }

                // Return just the balance for non-AJAX requests
                return $balanceFloat;
            } else {
                // Log the error
                Log::warning('Failed to fetch N3tdata balance', [
                    'error' => $balanceResult['message'] ?? 'Unknown error'
                ]);

                // Return JSON response for AJAX requests
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'balance' => 0.0,
                        'message' => $balanceResult['message'] ?? 'Failed to fetch balance',
                        'timestamp' => now()->toISOString()
                    ], 500);
                }

                return 0.0;
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception while fetching N3tdata balance', [
                'error' => $e->getMessage()
            ]);

            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'balance' => 0.0,
                    'message' => 'Connection error: ' . $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ], 500);
            }

            return 0.0;
        }
    }}
