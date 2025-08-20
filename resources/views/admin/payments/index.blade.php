@extends('layouts.admin')

@section('title', 'Manage Payments')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
        <p class="mt-2 text-gray-600">View and manage all user payments.</p>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700">Status</label>
            <select name="status" class="border-gray-300 rounded px-2 py-1">
                <option value="">All</option>
                <option value="completed" @if(request('status')=='completed') selected @endif>Completed</option>
                <option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
                <option value="failed" @if(request('status')=='failed') selected @endif>Failed</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700">Gateway</label>
            <select name="gateway" class="border-gray-300 rounded px-2 py-1">
                <option value="">All</option>
                <option value="paystack" @if(request('gateway')=='paystack') selected @endif>Paystack</option>
                <option value="nomba" @if(request('gateway')=='nomba') selected @endif>Nomba</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700">Type</label>
            <select name="type" class="border-gray-300 rounded px-2 py-1">
                <option value="">All</option>
                <option value="subscription" @if(request('type')=='subscription') selected @endif>Subscription</option>
                <option value="wallet_funding" @if(request('type')=='wallet_funding') selected @endif>Wallet Funding</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700">User/Reference</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, or Ref" class="border-gray-300 rounded px-2 py-1">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gateway</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">{{ $payment->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->user->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">{{ $payment->reference }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $payment->gateway }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">{{ str_replace('_', ' ', $payment->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₦{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : ($payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
