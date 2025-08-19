<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SimController;

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

// Authentication Routes
Route::middleware('guest')->group(function () {
    // User Authentication
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Admin Authentication
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});

// Logout (available to all authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Payment callback routes (accessible without authentication middleware for webhooks)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/callback/nomba', [WalletController::class, 'handleNombaCallback'])->name('payment.callback.nomba');
Route::post('/payment/webhook/nomba', [WalletController::class, 'handleNombaWebhook'])->name('payment.webhook.nomba');

// User Dashboard Routes
Route::middleware(['auth', 'user'])->group(function () {
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
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}/toggle-status', [AdminController::class, 'updateUserStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/credit-wallet', [AdminController::class, 'creditWallet'])->name('users.credit-wallet');
    Route::post('/users/{user}/debit-wallet', [AdminController::class, 'debitWallet'])->name('users.debit-wallet');

    // Plans Management
    Route::get('/plans', [AdminController::class, 'plans'])->name('plans');

    // Subscriptions Management
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions');

    // Payments Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');

    // Networks Management
    Route::get('/networks', [AdminController::class, 'networks'])->name('networks');
    Route::get('/networks/create', [AdminController::class, 'createNetwork'])->name('networks.create');
    Route::post('/networks', [AdminController::class, 'storeNetwork'])->name('networks.store');
    Route::get('/networks/{network}', [AdminController::class, 'showNetwork'])->name('networks.show');
    Route::get('/networks/{network}/edit', [AdminController::class, 'editNetwork'])->name('networks.edit');
    Route::put('/networks/{network}', [AdminController::class, 'updateNetwork'])->name('networks.update');
    Route::patch('/networks/{network}/toggle-status', [AdminController::class, 'toggleNetworkStatus'])->name('networks.toggle-status');
    Route::delete('/networks/{network}', [AdminController::class, 'destroyNetwork'])->name('networks.destroy');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});
