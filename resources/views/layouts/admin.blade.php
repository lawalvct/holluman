<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" x-init="$watch('sidebarOpen', value => {
    if (value) {
        document.body.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
    }
})">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Veasat</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary-color: #2563EB;
            --primary-hover: #1D4ED8;
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .hover\:bg-primary-hover:hover { background-color: var(--primary-hover); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-primary transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex items-center justify-center h-16 bg-primary-hover">
                <div class="flex items-center space-x-2">
                    @if(!empty($companySettings['logo']))
                        <img src="{{ $companySettings['logo'] }}" alt="{{ $companySettings['name'] ?? 'Company Logo' }}" class="h-8 w-auto">
                    @endif
                    <h1 class="text-white text-xl font-bold">{{ $companySettings['name'] ?? 'Admin' }}</h1>
                </div>
            </div>
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <!-- Dashboard -->
                    @if(auth()->user()->hasPermission('dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    @endif

                    <!-- Users Management -->
                    @if(auth()->user()->hasPermission('users'))
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.users*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    @endif

                    <!-- User Sims -->
                    @if(auth()->user()->hasPermission('sims'))
                        <a href="{{ route('admin.sims') }}" class="{{ request()->routeIs('admin.sims*') ? 'bg-primary-200' : '' }} flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md">
                            <i class="fas fa-sim-card mr-3"></i>
                            User Sims
                        </a>
                    @endif

                    <!-- Subscription Plans -->
                    @if(auth()->user()->hasPermission('plans'))
                    <a href="{{ route('admin.plans.index') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.plans*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-box mr-3"></i>
                        Plans
                    </a>
                    @endif

                    <!-- Subscriptions -->
                    @if(auth()->user()->hasPermission('subscriptions'))
                    <a href="{{ route('admin.subscriptions') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.subscriptions*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-sync-alt mr-3"></i>
                        Subscriptions
                    </a>
                    @endif

                    <!-- Payments -->
                    @if(auth()->user()->hasPermission('payments'))
                    <a href="{{ route('admin.payments') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.payments*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-credit-card mr-3"></i>
                        Payments
                    </a>
                    @endif

                    <!-- Networks -->
                    @if(auth()->user()->hasPermission('networks'))
                    <a href="{{ route('admin.networks') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.networks*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-network-wired mr-3"></i>
                        Networks
                    </a>
                    @endif

                    <!-- Reports -->
                    @if(auth()->user()->hasPermission('reports'))
                    <a href="{{ route('admin.reports') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.reports*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Reports
                    </a>
                    @endif

                    <!-- Settings -->
                    @if(auth()->user()->hasPermission('settings'))
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.settings*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                    @endif

                    <!-- Admin Management (Superadmin Only) -->
                    @if(auth()->user()->hasPermission('admin_management'))
                    <a href="{{ route('admin.admins') }}"
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.admins*') ? 'bg-primary-hover text-white' : '' }}">
                        <i class="fas fa-user-shield mr-3"></i>
                        Admin Management
                    </a>
                    @endif
                </div>

                <!-- Settings Section -->
                <div class="mt-8 pt-8 border-t border-blue-400">
                    <div class="px-4 space-y-2">
                        <a href="{{ route('admin.settings') }}"
                           class="flex items-center px-4 py-2 text-gray-300 hover:bg-primary-hover hover:text-white rounded-md {{ request()->routeIs('admin.settings*') ? 'bg-primary-hover text-white' : '' }}">
                            <i class="fas fa-cogs mr-3"></i>
                            Settings
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Sidebar overlay for mobile -->
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"></div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-3">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Page title -->
                    <div class="flex-1 lg:flex lg:items-center lg:justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 lg:hidden">@yield('title', 'Admin Panel')</h2>
                    </div>

                    <!-- N3tdata Balance Display -->
                    <div class="flex items-center space-x-2 bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">N3tdata Balance:</span>
                        <span id="n3tdata-balance" class="text-sm font-bold text-green-600">₦0.00</span>
                        <button id="refresh-balance"
                                class="ml-2 p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors duration-200"
                                title="Refresh Balance">
                            <i class="fas fa-sync-alt text-xs"></i>
                        </button>
                        <span id="balance-status" class="text-xs text-gray-400 hidden">Loading...</span>
                    </div>

                <!-- User menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-primary">
                            <i class="fas fa-bell"></i>
                        </button>

                        <!-- Profile dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center max-w-xs bg-white rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down ml-2 h-4 w-4 text-gray-400"></i>
                            </button>

                            <div x-show="open"
                                 x-cloak
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        User Dashboard
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile Settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- N3tdata Balance Management Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const balanceElement = document.getElementById('n3tdata-balance');
            const refreshButton = document.getElementById('refresh-balance');
            const statusElement = document.getElementById('balance-status');
            const BALANCE_CACHE_KEY = 'n3tdata_balance_cache';
            const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes in milliseconds

            // Function to format currency
            function formatCurrency(amount) {
                return '₦' + parseFloat(amount).toLocaleString('en-NG', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Function to update balance display
            function updateBalanceDisplay(balance, fromCache = false) {
                balanceElement.textContent = formatCurrency(balance);
                balanceElement.className = parseFloat(balance) > 0
                    ? 'text-sm font-bold text-green-600'
                    : 'text-sm font-bold text-red-600';

                if (fromCache) {
                    statusElement.textContent = 'Cached';
                    statusElement.className = 'text-xs text-blue-500';
                    statusElement.classList.remove('hidden');
                    setTimeout(() => statusElement.classList.add('hidden'), 2000);
                }
            }

            // Function to show loading state
            function showLoading() {
                refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';
                refreshButton.disabled = true;
                statusElement.textContent = 'Loading...';
                statusElement.className = 'text-xs text-gray-500';
                statusElement.classList.remove('hidden');
            }

            // Function to hide loading state
            function hideLoading() {
                refreshButton.innerHTML = '<i class="fas fa-sync-alt text-xs"></i>';
                refreshButton.disabled = false;
                setTimeout(() => statusElement.classList.add('hidden'), 2000);
            }

            // Function to show error state
            function showError(message) {
                statusElement.textContent = message || 'Error';
                statusElement.className = 'text-xs text-red-500';
                statusElement.classList.remove('hidden');
                balanceElement.textContent = '₦0.00';
                balanceElement.className = 'text-sm font-bold text-red-600';
            }

            // Function to get cached balance
            function getCachedBalance() {
                try {
                    const cached = localStorage.getItem(BALANCE_CACHE_KEY);
                    if (cached) {
                        const data = JSON.parse(cached);
                        const now = new Date().getTime();

                        if (now - data.timestamp < CACHE_DURATION) {
                            return data.balance;
                        } else {
                            localStorage.removeItem(BALANCE_CACHE_KEY);
                        }
                    }
                } catch (error) {
                    console.error('Error reading cached balance:', error);
                    localStorage.removeItem(BALANCE_CACHE_KEY);
                }
                return null;
            }

            // Function to cache balance
            function cacheBalance(balance) {
                try {
                    const data = {
                        balance: balance,
                        timestamp: new Date().getTime()
                    };
                    localStorage.setItem(BALANCE_CACHE_KEY, JSON.stringify(data));
                } catch (error) {
                    console.error('Error caching balance:', error);
                }
            }

            // Function to fetch balance from API
            function fetchBalance(forceRefresh = false) {
                if (!forceRefresh) {
                    const cached = getCachedBalance();
                    if (cached !== null) {
                        updateBalanceDisplay(cached, true);
                        return;
                    }
                }

                showLoading();

                fetch('{{ route("admin.n3tdata.balance") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const balance = data.balance || 0;
                        updateBalanceDisplay(balance);
                        cacheBalance(balance);

                        statusElement.textContent = 'Updated';
                        statusElement.className = 'text-xs text-green-500';
                    } else {
                        showError(data.message || 'Failed to fetch balance');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error fetching balance:', error);
                    showError('Connection failed');
                });
            }

            // Event listener for refresh button
            refreshButton.addEventListener('click', function() {
                fetchBalance(true); // Force refresh
            });

            // Load balance on page load
            fetchBalance();

            // Auto-refresh every 10 minutes
            setInterval(() => {
                fetchBalance(true);
            }, 10 * 60 * 1000);
        });
    </script>
</body>
</html>
