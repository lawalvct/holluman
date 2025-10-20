@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{
    showWelcomeModal: false,
    hasSeenModal: localStorage.getItem('hasSeenWelcomeModal') === 'true'
}" x-init="
    @if($user->sims->count() === 0)
        if (!hasSeenModal) {
            setTimeout(() => {
                showWelcomeModal = true;
            }, 10000);
        }
    @endif
">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Welcome back, {{ $user->name }}!
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your internet subscription and account
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Wallet Balance -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Wallet Balance
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $wallet->formatted_balance }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('wallet') }}" class="font-medium text-blue-700 hover:text-blue-900">
                        Manage Wallet
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Subscription -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Current Plan
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($activeSubscription)
                                    {{ $activeSubscription->subscriptionPlan->name }}
                                @else
                                    No Active Plan
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    @if($activeSubscription)
                        <span class="text-green-600 font-medium">
                            {{ $activeSubscription->days_remaining }} days remaining
                        </span>
                    @else
                        <a href="{{ route('plans') }}" class="font-medium text-blue-700 hover:text-blue-900">
                            Choose a Plan
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Subscriptions -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Subscriptions
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $recentSubscriptions->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('subscriptions.history') }}" class="font-medium text-blue-700 hover:text-blue-900">
                        View History
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Account Status
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($user->is_active)
                                    <span class="text-green-600">Active</span>
                                @else
                                    <span class="text-red-600">Inactive</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Recent Wallet Transactions
                </h3>
                @if($recentTransactions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTransactions as $transaction)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($transaction->type === 'credit')
                                        <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $transaction->description }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $transaction->created_at->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->formatted_amount }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('wallet.transactions') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            View all transactions →
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">No transactions yet</p>
                        <a href="{{ route('wallet') }}" class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            Fund your wallet to get started
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    @if(!$activeSubscription)
                    <a href="{{ route('plans') }}" class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Subscribe to a Plan
                    </a>
                    @endif

                    <a href="{{ route('wallet') }}" class="block w-full bg-green-600 text-white text-center px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        Fund Wallet
                    </a>

                    <a href="{{ route('subscriptions.history') }}" class="block w-full bg-gray-600 text-white text-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        View Subscription History
                    </a>

                    <a href="{{ route('wallet.transactions') }}" class="block w-full bg-purple-600 text-white text-center px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                        View Transaction History
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($featuredPlans->count() > 0 && !$activeSubscription)
    <!-- Featured Plans -->
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Popular Plans
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($featuredPlans as $plan)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors">
                        <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                        <p class="text-2xl font-bold text-blue-600 mt-2">{{ $plan->formatted_price }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $plan->speed }} • {{ $plan->data_limit }}</p>
                        <a href="{{ route('plans.show', $plan) }}" class="block w-full mt-3 bg-blue-600 text-white text-center px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors">
                            Subscribe Now
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Welcome Modal for New Users -->
    <div x-show="showWelcomeModal"
         x-cloak
         class="fixed z-50 inset-0 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true"
         @keydown.escape.window="showWelcomeModal = false">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showWelcomeModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 aria-hidden="true"
                 @click="showWelcomeModal = false; localStorage.setItem('hasSeenWelcomeModal', 'true')"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showWelcomeModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <!-- Close button -->
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button"
                            @click="showWelcomeModal = false; localStorage.setItem('hasSeenWelcomeModal', 'true')"
                            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 sm:mx-0 sm:h-16 sm:w-16">
                        <svg class="h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-2" id="modal-title">
                            Welcome to Veesta! 🎉
                        </h3>
                        <div class="mt-4">
                            <p class="text-base text-gray-700 mb-3">
                                Hi <span class="font-semibold text-blue-600">{{ $user->name }}</span>, we're excited to have you here!
                            </p>
                            <p class="text-sm text-gray-600 mb-4">
                                To get started with your internet subscription, you'll need to add your camera-enabled phone number (SIM).
                            </p>

                            <!-- Features list -->
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold text-blue-900 mb-1">Quick Setup - Just 2 Steps:</h4>
                                        <ul class="text-sm text-blue-800 space-y-1">
                                            <li class="flex items-center">
                                                <span class="mr-2">1️⃣</span> Add your SIM/Camera number
                                            </li>
                                            <li class="flex items-center">
                                                <span class="mr-2">2️⃣</span> Choose a data plan and subscribe
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 italic">
                                💡 Don't worry, you can add multiple SIM numbers later!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="mt-6 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                    <a href="{{ route('sims.create') }}"
                       class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-base font-medium text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm transition-all transform hover:scale-105">
                        <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add SIM Number Now
                    </a>
                    <a href="{{ route('plans') }}"
                       class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        <svg class="mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View Plans First
                    </a>
                    <button type="button"
                            @click="showWelcomeModal = false; localStorage.setItem('hasSeenWelcomeModal', 'true')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Maybe Later
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
