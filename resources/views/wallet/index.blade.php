@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Nomba Configuration Status -->
    @if(!$nombaConfigured)
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Nomba Payment Configuration Required
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Nomba payment gateway is not fully configured. Please contact administrator to set up Nomba credentials for seamless payments.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    My Wallet
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your wallet balance and transaction history
                </p>
            </div>
        </div>
    </div>

    <!-- Wallet Balance Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">Wallet Balance</h3>
                <p class="text-3xl font-bold mt-2">{{ $wallet->formatted_balance }}</p>
                <p class="text-blue-100 text-sm mt-1">Available for subscriptions</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Fund Wallet -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-gray-900">Fund Wallet</h3>
                    <p class="text-sm text-gray-500 mt-1">Add money to your wallet using Paystack or Nomba</p>
                    <div class="mt-4">
                        <button type="button" onclick="showFundModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Fund Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-gray-900">Buy Subscription</h3>
                    <p class="text-sm text-gray-500 mt-1">Browse and purchase internet subscription plans</p>
                    <div class="mt-4">
                        <a href="{{ route('plans') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Browse Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Transaction History</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Recent wallet transactions and payments</p>
                </div>
                <div class="flex space-x-2">
                    <select class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Transactions</option>
                        <option value="credit">Credits</option>
                        <option value="debit">Debits</option>
                    </select>
                </div>
            </div>
        </div>

        @if($transactions->count() > 0)
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($transaction->type === 'credit')
                                        <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $transaction->description }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $transaction->created_at->format('M j, Y g:i A') }}
                                        @if($transaction->reference)
                                            • Ref: {{ $transaction->reference }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="text-right">
                                    <div class="text-sm font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Balance: {{ $transaction->formatted_balance_after }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($transaction->metadata && is_array($transaction->metadata))
                            <div class="mt-3 pl-14">
                                <div class="bg-gray-50 rounded-md p-3">
                                    <div class="text-xs text-gray-600">
                                        @foreach($transaction->metadata as $key => $value)
                                            <div><span class="font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        @else
            <!-- No Transactions -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Transactions Yet</h3>
                <p class="mt-2 text-gray-500">Start by funding your wallet to see transaction history.</p>
                <div class="mt-6">
                    <button type="button" onclick="showFundModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Fund Wallet
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Fund Wallet Modal -->
<div id="fundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Fund Wallet</h3>
                <button onclick="hideFundModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('wallet.fund') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-semibold text-gray-800 mb-3">Amount (NGN)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">₦</span>
                        <input type="number" name="amount" id="amount" min="100" step="1" required
                               class="w-full pl-8 pr-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-lg font-medium"
                               placeholder="0.00">
                    </div>
                    <p class="mt-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-md">💡 Minimum amount: ₦100</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-800 mb-3">Payment Method</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="gateway" value="nomba" class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300" {{ !$nombaConfigured ? 'disabled' : 'checked' }}>
                            <div class="ml-3">
                                <span class="text-sm font-medium {{ !$nombaConfigured ? 'text-gray-400' : 'text-gray-800' }}">
                                    Nomba Payment Gateway
                                </span>
                                @if(!$nombaConfigured)
                                    <span class="block text-xs text-red-500 mt-1">⚠️ Not configured</span>
                                @else
                                    <span class="block text-xs text-gray-500 mt-1">Secure card & bank transfer payments</span>
                                @endif
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideFundModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showFundModal() {
    document.getElementById('fundModal').classList.remove('hidden');
}

function hideFundModal() {
    document.getElementById('fundModal').classList.add('hidden');
}
</script>
@endsection
