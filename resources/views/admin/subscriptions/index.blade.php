@extends('layouts.admin')

@section('title', 'Manage Subscriptions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Subscriptions</h1>
        <p class="mt-2 text-gray-600">View and manage all user subscriptions.</p>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700">Status</label>
            <select name="status" class="border-gray-300 rounded px-2 py-1">
                <option value="">All</option>
                <option value="active" @if(request('status')=='active') selected @endif>Active</option>
                <option value="expired" @if(request('status')=='expired') selected @endif>Expired</option>
                <option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700">Plan</label>
            <select name="plan_id" class="border-gray-300 rounded px-2 py-1">
                <option value="">All</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @if(request('plan_id')==$plan->id) selected @endif>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700">User</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Email" class="border-gray-300 rounded px-2 py-1">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">{{ $subscription->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $subscription->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $subscription->subscriptionPlan->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded {{ $subscription->status == 'active' ? 'bg-green-100 text-green-800' : ($subscription->status == 'expired' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₦{{ number_format($subscription->amount_paid, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection
