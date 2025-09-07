<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companySettings['name'] ?? 'Veasat' }} - Stay Connected</title>
    <meta name="description" content="{{ $metaSettings['description'] ?? 'Professional internet data subscription service' }}">
    <meta name="keywords" content="{{ $metaSettings['keywords'] ?? 'internet, data, subscription, nigeria' }}">
    <meta name="author" content="{{ $metaSettings['author'] ?? $companySettings['name'] ?? 'Veasat' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $companySettings['logo'] ?? asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ $companySettings['logo'] ?? asset('favicon.ico') }}">

    <!-- OpenGraph Tags for WhatsApp and Social Media -->
    <meta property="og:title" content="{{ $companySettings['name'] ?? 'Veasat' }} - Stay Connected">
    <meta property="og:description" content="{{ $metaSettings['description'] ?? 'Professional internet data subscription service. Stay connected wherever you go with high-speed internet.' }}">
    <meta property="og:image" content="{{ $companySettings['logo'] ?? asset('images/logo.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companySettings['name'] ?? 'Veasat' }}">

    <!-- WhatsApp specific -->
    <meta property="og:image:width" content="300">
    <meta property="og:image:height" content="300">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $companySettings['name'] ?? 'Veasat' }} - Stay Connected">
    <meta name="twitter:description" content="{{ $metaSettings['description'] ?? 'Professional internet data subscription service' }}">
    <meta name="twitter:image" content="{{ $companySettings['logo'] ?? asset('images/logo.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
    <meta name="google-site-verification" content="D91uSsHJs_ApKAxjoNcI0K0oz0wF4sqGgZ6ONkhaADI" />
</head>
<body class="bg-gray-50 font-sans">

    <!-- Navigation -->
    <nav class="bg-white shadow-md" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center text-2xl font-bold text-primary">
                        <img src="{{ $companySettings['logo'] ?? asset('images/logo.png') }}"
                             alt="{{ $companySettings['name'] ?? 'Veasat' }} Logo"
                             class="h-8">
                        <span class="ml-2">{{ $companySettings['name'] ?? 'Veasat' }}</span>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#features" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Features</a>
                        <a href="#plans" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Plans</a>
                        <a href="#contact" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                        @guest
                            <a href="{{ route('login') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="{{ route('register') }}" class="bg-primary text-white hover:bg-primary-hover px-3 py-2 rounded-md text-sm font-medium">Get Started</a>
                        @else
                             <a href="{{ route('dashboard') }}" class="bg-primary text-white hover:bg-primary-hover px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        @endguest
                    </div>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-primary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                <a href="#features" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Features</a>
                <a href="#plans" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Plans</a>
                <a href="#contact" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                @guest
                    <a href="{{ route('login') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 block px-3 py-2 rounded-md text-base font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-primary text-white hover:bg-primary-hover block px-3 py-2 rounded-md text-base font-medium">Get Started</a>
                @else
                    <a href="{{ route('dashboard') }}" class="bg-primary text-white hover:bg-primary-hover block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-white">
        <div class="max-w-7xl mx-auto py-20 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Login Form -->
                @guest
                <div class="bg-gray-50 p-8 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Sign In</h2>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" name="email" type="email" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary @error('email') border-red-500 @enderror"
                                   value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input id="password" name="password" type="password" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary">
                            Sign In
                        </button>
                    </form>

                    <p class="mt-4 text-center text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-primary hover:underline">Sign up</a>
                    </p>
                </div>
                @else
                <div class="bg-green-50 p-8 rounded-lg shadow-lg text-center">
                    <h2 class="text-2xl font-bold text-green-800 mb-4">Welcome back!</h2>
                    <p class="text-green-700 mb-6">You're already signed in.</p>
                    <a href="{{ route('dashboard') }}" class="bg-primary text-white py-2 px-6 rounded-md hover:bg-primary-hover">
                        Go to Dashboard
                    </a>
                </div>
                @endguest

                <!-- Hero Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Seamless Internet with</span>
                        <span class="block text-primary">{{ $companySettings['name'] ?? 'Veasat' }}</span>
                    </h1>
                    <p class="mt-4 text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl">
                        Stay connected wherever you go. Subscribe to high-speed internet directly on your Nigerian camera phone number. No extra hardware needed.
                    </p>
                    <div class="mt-5 sm:flex sm:justify-center lg:justify-start md:mt-8">
                        <div class="rounded-md shadow">
                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary-hover md:py-4 md:text-lg md:px-10">
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">How It Works</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Get Connected in 3 Simple Steps
                </p>
            </div>
            <div class="mt-12">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary text-white mx-auto">
                           1
                        </div>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Choose a Plan</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Select the data plan that best fits your needs.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary text-white mx-auto">
                            2
                        </div>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Enter Phone Number</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Provide your camera-enabled phone number for activation.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary text-white mx-auto">
                            3
                        </div>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Enjoy High-Speed Internet</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Get instant access to our reliable network.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">Features</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Everything You Need to Stay Online
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <dt>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Instant Activation</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Get your internet activated in minutes. Just subscribe with your phone number and you're ready to go.
                        </dd>
                    </div>
                    <div class="relative">
                        <dt>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Affordable Data Plans</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Choose from a variety of budget-friendly plans that suit your data needs and wallet.
                        </dd>
                    </div>
                    <div class="relative">
                        <dt>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Nationwide Coverage</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Enjoy reliable internet access across Nigeria, whether you're in the city or a rural area.
                        </dd>
                    </div>
                    <div class="relative">
                        <dt>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">24/7 Support</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Our dedicated support team is always available to help you with any issues or questions.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- Plans Section -->
    <section id="plans" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">Choose Your Plan</h2>
                <p class="mt-4 text-lg text-gray-600">Simple, transparent pricing.</p>
            </div>
            @if(isset($plans) && $plans->count() > 0)
            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($plans as $plan)
                <div class="relative border @if($plan->sort_order == 2) border-primary @else border-gray-200 @endif rounded-lg shadow-sm divide-y divide-gray-200">
                    @if($plan->sort_order == 2)
                        <div class="absolute top-0 right-0 -mt-3 -mr-3">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-primary text-white">
                                Most Popular
                            </span>
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="text-2xl leading-6 font-medium text-gray-900">{{ $plan->name }}</h3>
                        <p class="mt-4 text-sm text-gray-500">{{ $plan->description }}</p>
                        <p class="mt-8">
                            <span class="text-4xl font-extrabold text-gray-900">{{ $plan->formatted_price }}</span>
                            <span class="text-base font-medium text-gray-500">/month</span>
                        </p>
                        <a href="{{ route('register') }}" class="mt-8 block w-full bg-primary border border-transparent rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-primary-hover">Get Started</a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Contact Section -->
    <footer id="contact" class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold">{{ $companySettings['name'] ?? 'Veasat' }}</h3>
                    <p class="mt-2 text-gray-400">Your partner in connectivity.</p>
                    @if($companySettings['address'])
                        <p class="mt-2 text-gray-400 text-sm">{{ $companySettings['address'] }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Contact Us</h3>
                    <ul class="mt-2 space-y-2">
                        @if($companySettings['email'])
                            <li>Email: {{ $companySettings['email'] }}</li>
                        @endif
                        @if($companySettings['support_phone'])
                            <li>Phone: {{ $companySettings['support_phone'] }}</li>
                        @endif
                        @if($companySettings['support_email'] && $companySettings['support_email'] !== $companySettings['email'])
                            <li>Support: {{ $companySettings['support_email'] }}</li>
                        @endif
                        @if(!$companySettings['email'] && !$companySettings['support_phone'])
                            <li>Email: support@veasat.com</li>
                            <li>Phone: +234 800 123 4567</li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Follow Us</h3>
                    <div class="flex space-x-4 mt-2">
                        <a href="#" class="text-gray-400 hover:text-white">Twitter</a>
                        <a href="#" class="text-gray-400 hover:text-white">Facebook</a>
                        <a href="#" class="text-gray-400 hover:text-white">Instagram</a>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-700 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $companySettings['name'] ?? 'Veasat' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
