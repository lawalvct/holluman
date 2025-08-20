@extends('layouts.admin')

@section('title', 'Manage Subscription Plans')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Subscription Plans</h1>
        <p class="mt-2 text-gray-600">Manage all available subscription plans.</p>
    </div>

    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Total Plans</div>
            <div class="text-2xl font-bold">{{ $stats['total_plans'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Active Plans</div>
            <div class="text-2xl font-bold">{{ $stats['active_plans'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Total Subscriptions</div>
            <div class="text-2xl font-bold">{{ $stats['total_subscriptions'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Total Revenue</div>
            <div class="text-2xl font-bold">₦{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
        </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active Subs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($plans as $plan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $plan->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₦{{ number_format($plan->price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $plan->duration_in_days }} days</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $plan->subscriptions_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($plan->is_active)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $plan->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No plans found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $plans->links() }}
        </div>
    </div>
</div>
@endsection
