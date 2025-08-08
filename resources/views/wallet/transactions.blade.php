@extends('layouts.app')

@section('title', 'Wallet Transactions')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Wallet Transactions
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    View your complete transaction history
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('wallet') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Wallet
                </a>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($transactions->count() > 0)
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                    <li class="px-4 py-6 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($transaction->type === 'credit')
                                        <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-base font-medium text-gray-900">
                                        {{ $transaction->description }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $transaction->created_at->format('M j, Y g:i A') }}
                                    </div>
                                    @if($transaction->reference)
                                        <div class="text-xs text-gray-400 mt-1">
                                            Reference: {{ $transaction->reference }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="text-right">
                                    <div class="text-lg font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Status:
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' :
                                               ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        @if($transaction->payment)
                            <div class="mt-4 pl-16">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Payment Method:</span>
                                            <span class="ml-1 text-gray-900">{{ ucfirst($transaction->payment->gateway) }}</span>
                                        </div>
                                        @if($transaction->payment->gateway_reference)
                                            <div>
                                                <span class="font-medium text-gray-700">Gateway Ref:</span>
                                                <span class="ml-1 text-gray-900 font-mono text-xs">{{ $transaction->payment->gateway_reference }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-medium text-gray-700">Payment Status:</span>
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $transaction->payment->status === 'successful' ? 'bg-green-100 text-green-800' :
                                                   ($transaction->payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($transaction->payment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $transactions->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions found</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't made any wallet transactions yet.</p>
                <div class="mt-6">
                    <a href="{{ route('wallet') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Fund Your Wallet
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
