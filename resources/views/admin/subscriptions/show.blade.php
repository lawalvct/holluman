@extends('layouts.admin')

@section('title', 'Subscription Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.subscriptions') }}" class="text-blue-600 hover:text-blue-900">&larr; Back to Subscriptions</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Subscription Details</h1>
    </div>
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-2 text-gray-500 text-xs">User</div>
                <div class="font-semibold text-lg">{{ $subscription->user->name }}</div>
                <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Plan</div>
                <div class="font-semibold text-lg">{{ $subscription->subscriptionPlan->name ?? '-' }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Status</div>
                <span class="px-2 py-1 text-xs rounded {{ $subscription->status == 'active' ? 'bg-green-100 text-green-800' : ($subscription->status == 'expired' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($subscription->status) }}
                </span>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Amount Paid</div>
                <div class="font-semibold">₦{{ number_format($subscription->amount_paid, 2) }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Start Date</div>
                <div>{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : '-' }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">End Date</div>
                <div>{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : '-' }}</div>
            </div>
        </div>
    </div>

    <!-- N3tdata Information -->
    <div class="bg-white rounded shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">N3tdata Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-2 text-gray-500 text-xs">N3tdata Status</div>
                @if($subscription->n3tdata_status)
                    <span class="px-2 py-1 text-xs rounded {{ $subscription->n3tdata_status == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($subscription->n3tdata_status) }}
                    </span>
                @else
                    <span class="text-gray-400">Not processed</span>
                @endif
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Data Plan</div>
                <div>{{ $subscription->n3tdata_plan ?? '-' }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">N3tdata Amount</div>
                <div>{{ $subscription->n3tdata_amount ? '₦' . number_format($subscription->n3tdata_amount, 2) : '-' }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Phone Number</div>
                <div>{{ $subscription->n3tdata_phone_number ?? $subscription->subscriber_phone ?? '-' }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Request ID</div>
                <div class="text-sm font-mono">{{ $subscription->n3tdata_request_id ?? '-' }}</div>
            </div>
            <div>
                <div class="mb-2 text-gray-500 text-xs">Data Activated</div>
                <div>{{ $subscription->data_activated_at ? $subscription->data_activated_at->format('M d, Y H:i') : '-' }}</div>
            </div>
        </div>

        @if($subscription->n3tdata_response)
            <div class="mt-6">
                <div class="mb-2 text-gray-500 text-xs">Full N3tdata Response</div>
                <div class="bg-gray-100 p-3 rounded text-xs font-mono overflow-x-auto">
                    {{ json_encode($subscription->n3tdata_response, JSON_PRETTY_PRINT) }}
                </div>
            </div>
        @endif

        <!-- N3tdata Retry Button -->
        @if($subscription->n3tdata_status !== 'success' && in_array($subscription->status, ['active', 'expired']))
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800">N3tdata Activation Issue</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                The subscription payment was successful, but the data activation with N3tdata API has failed or is pending.
                                You can retry the activation using the button below.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.subscriptions.retry-n3tdata', $subscription) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm font-medium"
                            onclick="return confirm('Are you sure you want to retry N3tdata activation for this subscription?')">
                        🔄 Retry N3tdata Activation
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    This will reset the N3tdata status and attempt to activate the data subscription again using the N3tdata API.
                </p>
            </div>
        @elseif($subscription->n3tdata_status !== 'success' && $subscription->status === 'pending')
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Payment Pending</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                This subscription payment is still pending. N3tdata activation will be attempted automatically once payment is confirmed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($subscription->n3tdata_status !== 'success' && $subscription->status === 'cancelled')
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-800">Subscription Cancelled</h4>
                            <p class="text-sm text-gray-700 mt-1">
                                This subscription has been cancelled. N3tdata activation is not available for cancelled subscriptions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center text-green-600">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">N3tdata activation completed successfully</span>
                </div>
                @if($subscription->data_activated_at)
                    <p class="text-xs text-gray-500 mt-1">
                        Activated on {{ $subscription->data_activated_at->format('M d, Y \a\t H:i') }}
                    </p>
                @endif
            </div>
        @endif
    </div>
    <div class="flex space-x-4">
        <form action="{{ route('admin.subscriptions.update-status', $subscription) }}" method="POST">
            @csrf
            @method('PATCH')
            <select name="status" class="border-gray-300 rounded px-2 py-1">
                <option value="active" @if($subscription->status=='active') selected @endif>Active</option>
                <option value="expired" @if($subscription->status=='expired') selected @endif>Expired</option>
                <option value="cancelled" @if($subscription->status=='cancelled') selected @endif>Cancelled</option>
            </select>
            <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded">Update Status</button>
        </form>
        <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" onsubmit="return confirm('Delete this subscription?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
        </form>
    </div>
</div>
@endsection
