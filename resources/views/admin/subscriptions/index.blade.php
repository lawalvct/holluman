@extends('layouts.admin')

@section('title', 'Subscriptions Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Subscriptions Management</h1>
            <p class="text-gray-600 mt-2">View and manage all user subscriptions.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_subscriptions'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_subscriptions'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-hourglass-end text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Expired</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['expired_subscriptions'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cancelled</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['cancelled_subscriptions'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.subscriptions') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="active" @if(request('status')=='active') selected @endif>Active</option>
                        <option value="expired" @if(request('status')=='expired') selected @endif>Expired</option>
                        <option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">Plan</label>
                    <select id="plan_id" name="plan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" @if(request('plan_id')==$plan->id) selected @endif>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Name or Email"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N3tdata Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold">{{ $subscription->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $subscription->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $subscription->subscriptionPlan->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $subscription->status == 'active' ? 'bg-green-100 text-green-800' : ($subscription->status == 'expired' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subscription->n3tdata_status)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $subscription->n3tdata_status == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($subscription->n3tdata_status) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $subscription->n3tdata_plan ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $subscription->n3tdata_phone_number ?? $subscription->subscriber_phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>₦{{ number_format($subscription->amount_paid, 2) }}</div>
                                @if($subscription->n3tdata_amount)
                                    <div class="text-xs text-gray-500">N3t: ₦{{ number_format($subscription->n3tdata_amount, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center space-x-2 justify-end">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md transition-colors"
                                       title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    @if($subscription->n3tdata_status !== 'success' && in_array($subscription->status, ['active', 'expired']))
                                        <form action="{{ route('admin.subscriptions.retry-n3tdata', $subscription) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-2 py-1 bg-orange-100 text-orange-700 hover:bg-orange-200 rounded-md transition-colors text-xs"
                                                    title="Retry N3tdata Activation (Payment Successful)"
                                                    onclick="return confirm('Retry N3tdata activation for this subscription?')">
                                                🔄
                                            </button>
                                        </form>
                                    @elseif($subscription->n3tdata_status !== 'success' && $subscription->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs" title="Payment Pending">
                                            ⏳
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">No subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection
