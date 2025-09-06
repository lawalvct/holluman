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
