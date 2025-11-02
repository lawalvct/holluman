<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\CronController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page - show available plans
Route::get('/', [SubscriptionController::class, 'welcome'])->name('welcome');

// Cron Job Endpoints (Public - Protected by Token)
Route::prefix('cron')->name('cron.')->group(function () {
    Route::get('/health', [CronController::class, 'health'])->name('health');
    Route::get('/update-subscriptions', [CronController::class, 'updateSubscriptions'])->name('update-subscriptions');
    Route::get('/subscription-stats', [CronController::class, 'getSubscriptionStats'])->name('subscription-stats');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // User Authentication
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Admin Authentication
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});

// Logout (available to all authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.resend');
});

// Payment callback routes (accessible without authentication middleware for webhooks)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/callback/nomba', [WalletController::class, 'handleNombaCallback'])->name('payment.callback.nomba');
Route::get('/payment/callback/nomba/subscription', [SubscriptionController::class, 'handleNombaCallback'])->name('payment.callback.nomba.subscription');
Route::post('/payment/webhook/nomba', [WalletController::class, 'handleNombaWebhook'])->name('payment.webhook.nomba');

// User Dashboard Routes
Route::middleware(['auth', 'user', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Subscription Routes
    Route::get('/plans', [SubscriptionController::class, 'index'])->name('plans');


    // SIM Management Routes
    Route::get('/sims', [SimController::class, 'index'])->name('sims');
    Route::get('/sims/create', [SimController::class, 'create'])->name('sims.create');
    Route::post('/sims', [SimController::class, 'store'])->name('sims.store');
    Route::get('/sims/{sim}/edit', [SimController::class, 'edit'])->name('sims.edit');
    Route::put('/sims/{sim}', [SimController::class, 'update'])->name('sims.update');
    Route::delete('/sims/{sim}', [SimController::class, 'destroy'])->name('sims.destroy');

    Route::get('/plans/{plan}', [SubscriptionController::class, 'show'])->name('plans.show');
    Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::get('/subscriptions', [SubscriptionController::class, 'history'])->name('subscriptions.history');

    // Wallet Routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/fund', [WalletController::class, 'fund'])->name('wallet.fund');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
});

// Admin Dashboard Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users Management
    Route::middleware(['permission:users'])->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'updateUserStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/credit-wallet', [AdminController::class, 'creditWallet'])->name('users.credit-wallet');
        Route::post('/users/{user}/debit-wallet', [AdminController::class, 'debitWallet'])->name('users.debit-wallet');
    });

    // Plans Management
    Route::middleware(['permission:plans'])->group(function () {
        Route::resource('plans', \App\Http\Controllers\Admin\SubscriptionPlanController::class);
    });

    // Subscriptions Management
    Route::middleware(['permission:subscriptions'])->group(function () {
        Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/subscriptions/{subscription}', [AdminController::class, 'showSubscription'])->name('subscriptions.show');
        Route::patch('/subscriptions/{subscription}/status', [AdminController::class, 'updateSubscriptionStatus'])->name('subscriptions.update-status');
        Route::post('/subscriptions/{subscription}/retry-n3tdata', [AdminController::class, 'retryN3tDataActivation'])->name('subscriptions.retry-n3tdata');
        Route::post('/subscriptions/{subscription}/renew-n3tdata', [AdminController::class, 'renewN3tDataSubscription'])->name('subscriptions.renew-n3tdata');
        Route::delete('/subscriptions/{subscription}', [AdminController::class, 'destroySubscription'])->name('subscriptions.destroy');
    });

    // Payments Management
    Route::middleware(['permission:payments'])->group(function () {
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    });

    // Networks Management
    Route::middleware(['permission:networks'])->group(function () {
        Route::get('/networks', [AdminController::class, 'networks'])->name('networks');
        Route::get('/networks/create', [AdminController::class, 'createNetwork'])->name('networks.create');
        Route::post('/networks', [AdminController::class, 'storeNetwork'])->name('networks.store');
        Route::get('/networks/{network}', [AdminController::class, 'showNetwork'])->name('networks.show');
        Route::get('/networks/{network}/edit', [AdminController::class, 'editNetwork'])->name('networks.edit');
        Route::put('/networks/{network}', [AdminController::class, 'updateNetwork'])->name('networks.update');
        Route::patch('/networks/{network}/toggle-status', [AdminController::class, 'toggleNetworkStatus'])->name('networks.toggle-status');
        Route::delete('/networks/{network}', [AdminController::class, 'destroyNetwork'])->name('networks.destroy');
    });

    // Reports
    Route::middleware(['permission:reports'])->group(function () {
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });

    // Settings
    Route::middleware(['permission:settings'])->group(function () {
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'settings'])->name('settings');
    });

    // Admin Management (Superadmin only)
    Route::middleware(['permission:admin_management'])->group(function () {
        Route::get('/admins', [AdminController::class, 'admins'])->name('admins');
        Route::get('/admins/create', [AdminController::class, 'createAdmin'])->name('admins.create');
        Route::post('/admins', [AdminController::class, 'storeAdmin'])->name('admins.store');
        Route::get('/admins/{admin}/edit', [AdminController::class, 'editAdmin'])->name('admins.edit');
        Route::put('/admins/{admin}', [AdminController::class, 'updateAdmin'])->name('admins.update');
        Route::delete('/admins/{admin}', [AdminController::class, 'destroyAdmin'])->name('admins.destroy');
    });

    // N3tdata Balance API
    Route::get('/n3tdata-balance', [AdminController::class, 'getN3tdataBalance'])->name('n3tdata.balance');

    // User Sims Management
    Route::middleware(['permission:sims'])->group(function () {
        Route::get('/sims', [AdminController::class, 'sims'])->name('sims');
        Route::get('/sims/{sim}', [AdminController::class, 'showSim'])->name('sims.show');
        Route::get('/sims/{sim}/edit', [AdminController::class, 'editSim'])->name('sims.edit');
        Route::put('/sims/{sim}', [AdminController::class, 'updateSim'])->name('sims.update');
        Route::delete('/sims/{sim}', [AdminController::class, 'destroySim'])->name('sims.destroy');
    });

    // Test N3tdata API
    Route::get('/test-n3tdata', function() {
        $helper = new \App\Helpers\N3tDataHelper();

        // Test getting access token
        $tokenResult = $helper->getAccessToken();

        // Test getting balance (which also tests access token)
        $balanceResult = $helper->getBalance();

        return response()->json([
            'access_token_test' => $tokenResult,
            'balance_test' => $balanceResult,
            'timestamp' => now()->toDateTimeString()
        ], 200, [], JSON_PRETTY_PRINT);
    })->name('test.n3tdata');
});
