@extends('layouts.app')

@section('title', $plan->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('plans') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Plans
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Plan Details -->
        <div class="bg-white shadow-lg rounded-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ $plan->name }}</h1>
                <p class="mt-2 text-gray-600">{{ $plan->description }}</p>
            </div>

            <!-- Pricing -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center">
                    <span class="text-5xl font-extrabold text-blue-600">{{ $plan->formatted_price }}</span>
                    <span class="text-xl font-medium text-gray-500 ml-2">/{{ $plan->formatted_duration }}</span>
                </div>
            </div>

            <!-- Features -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">What's Included:</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Speed -->
                    <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Internet Speed</h4>
                            <p class="text-sm text-gray-600">{{ $plan->speed }}</p>
                        </div>
                    </div>

                    <!-- Data Limit -->
                    <div class="flex items-center p-4 bg-green-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Data Allowance</h4>
                            <p class="text-sm text-gray-600">{{ $plan->data_limit }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Features -->
                @if($plan->features && is_array($plan->features))
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Additional Features:</h4>
                        <ul class="space-y-2">
                            @foreach($plan->features as $feature)
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Subscription Form -->
        <div class="bg-white shadow-lg rounded-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Subscribe to {{ $plan->name }}</h2>

            @if($activeSubscription)
                <!-- User has active subscription -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Active Subscription</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                You currently have an active subscription to "{{ $activeSubscription->subscriptionPlan->name }}"
                                which expires on {{ $activeSubscription->end_date->format('M j, Y') }}.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Account Info -->
                <div class="mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Your Account</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Name:</span>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Email:</span>
                                <span class="font-medium">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Wallet Balance:</span>
                                <span class="font-medium text-green-600">{{ $userWallet->formatted_balance }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <form method="POST" action="{{ route('subscribe', $plan) }}" x-data="{ paymentMethod: 'wallet' }">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Choose Payment Method</label>
                        <div class="space-y-3">
                            <!-- Wallet Payment -->
                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $userWallet->hasSufficientBalance($plan->price) ? '' : 'opacity-50' }}">
                                <input type="radio" name="payment_method" value="wallet"
                                       x-model="paymentMethod"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       {{ $userWallet->hasSufficientBalance($plan->price) ? '' : 'disabled' }}>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900">Wallet Balance</span>
                                            <span class="block text-sm text-gray-500">Pay from your wallet balance</span>
                                        </div>
                                        <span class="text-lg font-semibold {{ $userWallet->hasSufficientBalance($plan->price) ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $userWallet->formatted_balance }}
                                        </span>
                                    </div>
                                    @if(!$userWallet->hasSufficientBalance($plan->price))
                                        <p class="text-xs text-red-600 mt-1">
                                            Insufficient balance. Need {{ number_format($plan->price - $userWallet->balance, 2) }} more.
                                        </p>
                                    @endif
                                </div>
                            </label>

                            <!-- Paystack Payment -->
                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="paystack"
                                       x-model="paymentMethod"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div class="ml-3 flex-1">
                                    <span class="block text-sm font-medium text-gray-900">Paystack</span>
                                    <span class="block text-sm text-gray-500">Pay with card, bank transfer, or USSD</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjMDA5NTg4Ii8+Cjwvc3ZnPgo=" alt="Paystack" class="h-6 w-6">
                                </div>
                            </label>

                            <!-- Nomba Payment -->
                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="nomba"
                                       x-model="paymentMethod"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div class="ml-3 flex-1">
                                    <span class="block text-sm font-medium text-gray-900">Nomba</span>
                                    <span class="block text-sm text-gray-500">Pay with Nomba payment gateway</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjRkY2QjAwIi8+Cjwvc3ZnPgo=" alt="Nomba" class="h-6 w-6">
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subscribe Button -->
                    <div class="mb-6">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <span x-show="paymentMethod === 'wallet'">Subscribe with Wallet</span>
                            <span x-show="paymentMethod === 'paystack'">Continue to Paystack</span>
                            <span x-show="paymentMethod === 'nomba'">Continue to Nomba</span>
                        </button>
                    </div>

                    <!-- Terms -->
                    <div class="text-xs text-gray-500 text-center">
                        By subscribing, you agree to our
                        <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a>
                        and
                        <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>.
                        Your subscription will automatically activate upon successful payment.
                    </div>
                </form>

                <!-- Fund Wallet Link -->
                @if(!$userWallet->hasSufficientBalance($plan->price))
                    <div class="mt-6 text-center">
                        <a href="{{ route('wallet') }}"
                           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Fund your wallet to pay with wallet balance
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Plan Comparison -->
    <div class="mt-12 bg-gray-50 rounded-xl p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">Need help choosing?</h3>
        <div class="text-center">
            <p class="text-gray-600 mb-4">Not sure if this plan is right for you? Compare all our plans to find the perfect fit.</p>
            <a href="{{ route('plans') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Compare All Plans
            </a>
        </div>
    </div>
</div>
@endsection
