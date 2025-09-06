@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-gray-500">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L9 5.414V17a1 1 0 102 0V5.414l5.293 5.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <span class="sr-only">Back</span>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('admin.users') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Users</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="mt-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600">User Details and Management</p>
                </div>
            </div>

            <div class="flex space-x-3">
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white
                                   {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $user->is_active ? 'red' : 'green' }}-500"
                            onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- User Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- User Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-xl font-medium text-gray-600">{{ substr($user->name, 0, 2) }}</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500">{{ $user->phone ?: 'No phone number' }}</p>
                    <div class="mt-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Joined</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">User ID</dt>
                        <dd class="mt-1 text-gray-900">#{{ $user->id }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Wallet Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Wallet</h3>
                <div class="flex space-x-2">
                    <button onclick="showWalletModal('credit')"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                        + Add Money
                    </button>
                    <button onclick="showWalletModal('debit')"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium">
                        - Debit
                    </button>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold text-gray-900">{{ $user->wallet->formatted_balance }}</div>
                <p class="text-sm text-gray-500">Current Balance</p>

                <!-- Quick Credit Options -->
                <div class="mt-3">
                    <p class="text-xs text-gray-500 mb-2">Quick Credit:</p>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="quickCredit(500)" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-medium">₦500</button>
                        <button onclick="quickCredit(1000)" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-medium">₦1,000</button>
                        <button onclick="quickCredit(2000)" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-medium">₦2,000</button>
                        <button onclick="quickCredit(5000)" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-medium">₦5,000</button>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Transactions</h4>
                @if($user->walletTransactions->count() > 0)
                    <div class="space-y-2">
                        @foreach($user->walletTransactions->take(3) as $transaction)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $transaction->description }}</span>
                                <span class="font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No transactions yet</p>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Spent</dt>
                    <dd class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_spent'], 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Subscriptions</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Active Subscription</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ $stats['active_subscription'] ? 'Yes' : 'No' }}</dd>
                </div>
            </dl>
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
                    <div class="mt-2 text-sm text-green-700 grid grid-cols-1 sm:grid-cols-3 gap-4">
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
    @endif

    <!-- Subscription History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Subscription History</h3>
        </div>

        @if($subscriptionHistory->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriptionHistory as $subscription)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->subscriptionPlan->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subscription->subscriptionPlan->speed }} • {{ $subscription->subscriptionPlan->data_limit }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($subscription->amount_paid, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $subscription->start_date->format('M j') }} - {{ $subscription->end_date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                    {{ $subscription->payment_method }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($subscription->status === 'active') bg-green-100 text-green-800
                                        @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                                        @elseif($subscription->status === 'cancelled') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subscription->created_at->format('M j, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($subscriptionHistory->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $subscriptionHistory->links() }}
                </div>
            @endif
        @else
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Subscriptions</h3>
                <p class="mt-2 text-gray-500">This user hasn't subscribed to any plans yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Wallet Management Modal -->
<div id="walletModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
                <button onclick="hideWalletModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="walletForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (NGN)</label>
                    <input type="number" name="amount" id="amount" min="1" step="0.01" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter amount">
                    <div id="balanceInfo" class="mt-1 text-xs text-gray-500"></div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" required
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Reason for this transaction"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideWalletModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2">
                        Process
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function quickCredit(amount) {
    if (confirm('Are you sure you want to credit ₦' + amount.toLocaleString() + ' to {{ $user->name }}\'s wallet?')) {
        // Create and submit a quick credit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.credit-wallet", $user) }}';

        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Amount
        const amountInput = document.createElement('input');
        amountInput.type = 'hidden';
        amountInput.name = 'amount';
        amountInput.value = amount;
        form.appendChild(amountInput);

        // Description
        const descInput = document.createElement('input');
        descInput.type = 'hidden';
        descInput.name = 'description';
        descInput.value = 'Quick credit of ₦' + amount.toLocaleString() + ' by admin';
        form.appendChild(descInput);

        document.body.appendChild(form);
        form.submit();
    }
}

function showWalletModal(type) {
    const modal = document.getElementById('walletModal');
    const form = document.getElementById('walletForm');
    const title = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const balanceInfo = document.getElementById('balanceInfo');
    const amountInput = document.getElementById('amount');

    if (type === 'credit') {
        title.textContent = 'Credit Wallet';
        form.action = '{{ route("admin.users.credit-wallet", $user) }}';
        submitBtn.textContent = 'Credit Wallet';
        submitBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        balanceInfo.textContent = 'Current balance: {{ $user->wallet->formatted_balance }}';
        amountInput.removeAttribute('max');
    } else {
        title.textContent = 'Debit Wallet';
        form.action = '{{ route("admin.users.debit-wallet", $user) }}';
        submitBtn.textContent = 'Debit Wallet';
        submitBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
        balanceInfo.textContent = 'Current balance: {{ $user->wallet->formatted_balance }} (Maximum debit: {{ $user->wallet->formatted_balance }})';
        amountInput.setAttribute('max', '{{ $user->wallet->balance }}');
    }

    modal.classList.remove('hidden');
}

function hideWalletModal() {
    document.getElementById('walletModal').classList.add('hidden');
    document.getElementById('walletForm').reset();
}
</script>
@endsection
