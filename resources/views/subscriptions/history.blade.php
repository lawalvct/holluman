@extends('layouts.app')

@section('title', 'Subscription History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Subscription History
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    View all your past and current subscriptions
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('plans') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Subscription
                </a>
            </div>
        </div>
    </div>

    <!-- Active Subscription -->
    @if($activeSubscription)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-green-900">
                        Active Subscription: {{ $activeSubscription->subscriptionPlan->name }}
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <span class="font-medium">Speed:</span> {{ $activeSubscription->subscriptionPlan->speed }}
                            </div>
                            <div>
                                <span class="font-medium">Data:</span> {{ $activeSubscription->subscriptionPlan->data_limit }}
                            </div>
                            <div>
                                <span class="font-medium">Expires:</span> {{ $activeSubscription->end_date->format('M j, Y') }}
                                <span class="text-green-600 font-medium">({{ $activeSubscription->days_remaining }} days left)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Subscriptions List -->
    @if($subscriptions->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($subscriptions as $subscription)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($subscription->status === 'active')
                                            <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @elseif($subscription->status === 'expired')
                                            <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @elseif($subscription->status === 'cancelled')
                                            <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $subscription->subscriptionPlan->name }}
                                        </h3>
                                        <div class="mt-1 flex items-center text-sm text-gray-500">
                                            <span class="mr-4">{{ $subscription->subscriptionPlan->speed }}</span>
                                            <span class="mr-4">{{ $subscription->subscriptionPlan->data_limit }}</span>
                                            <span>{{ $subscription->formatted_amount }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($subscription->status === 'active') bg-green-100 text-green-800
                                                @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                                                @elseif($subscription->status === 'cancelled') bg-gray-100 text-gray-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <div>Started: {{ $subscription->start_date->format('M j, Y') }}</div>
                                            <div>
                                                @if($subscription->status === 'active')
                                                    Expires: {{ $subscription->end_date->format('M j, Y') }}
                                                @else
                                                    Ended: {{ $subscription->end_date->format('M j, Y') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subscription Details -->
                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-500">Payment Method</dt>
                                        <dd class="mt-1 text-gray-900 capitalize">{{ $subscription->payment_method }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-gray-900">{{ $subscription->subscriptionPlan->formatted_duration }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Payment Reference</dt>
                                        <dd class="mt-1 text-gray-900 font-mono text-xs">{{ $subscription->payment_reference ?: 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Auto Renew</dt>
                                        <dd class="mt-1 text-gray-900">{{ $subscription->auto_renew ? 'Yes' : 'No' }}</dd>
                                    </div>
                                </div>

                                @if($subscription->subscriptionPlan->features)
                                    <div class="mt-4">
                                        <dt class="font-medium text-gray-500 mb-2">Features Included</dt>
                                        <dd class="text-gray-900">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($subscription->subscriptionPlan->features as $feature)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $feature }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </dd>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Pagination -->
        @if($subscriptions->hasPages())
            <div class="mt-8">
                {{ $subscriptions->links() }}
            </div>
        @endif
    @else
        <!-- No Subscriptions -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Subscriptions Yet</h3>
                <p class="mt-2 text-gray-500">You haven't subscribed to any plans yet. Browse our available plans to get started.</p>
                <div class="mt-6">
                    <a href="{{ route('plans') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Browse Plans
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
