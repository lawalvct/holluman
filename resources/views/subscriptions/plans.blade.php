@extends('layouts.app')

@section('title', 'Internet Plans')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
            Choose Your Internet Plan
        </h1>
        <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
            Select the perfect internet plan for your needs. All plans include 24/7 support and reliable connectivity.
        </p>
    </div>

    <!-- Plans Grid -->
    @if($plans->count() > 0)
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4">
            @foreach($plans as $plan)
                <div class="relative bg-white border border-gray-200 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 @if($plan->name === 'Premium Plan') ring-2 ring-blue-500 @endif">
                    @if($plan->name === 'Premium Plan')
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium bg-blue-500 text-white">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <div class="p-8">
                        <!-- Plan Header -->
                        <div class="text-center">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ $plan->description }}</p>
                        </div>

                        <!-- Pricing -->
                        <div class="mt-6 text-center">
                            <div class="flex items-center justify-center">
                                <span class="text-4xl font-extrabold text-gray-900">{{ $plan->formatted_price }}</span>
                                <span class="text-lg font-medium text-gray-500 ml-1">/{{ $plan->formatted_duration }}</span>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="mt-8">
                            <ul class="space-y-4">
                                <!-- Speed -->
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 font-medium">{{ $plan->speed }} Speed</span>
                                </li>

                                <!-- Data Limit -->
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700">{{ $plan->data_limit }} Data</span>
                                </li>

                                <!-- Additional Features -->
                                @if($plan->features && is_array($plan->features))
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-center">
                                            <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-gray-700">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <!-- CTA Button -->
                        <div class="mt-8">
                            <a href="{{ route('plans.show', $plan) }}"
                               class="block w-full text-center px-6 py-3 border border-transparent rounded-lg font-medium transition-colors duration-200
                                      @if($plan->name === 'Premium Plan')
                                          bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500
                                      @else
                                          bg-gray-100 text-gray-900 hover:bg-gray-200 focus:ring-2 focus:ring-gray-500
                                      @endif">
                                Choose {{ $plan->name }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Additional Information -->
        <div class="mt-16 bg-gray-50 rounded-2xl p-8">
            <div class="max-w-3xl mx-auto text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Why Choose Our Internet Service?</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h4 class="mt-4 text-lg font-semibold text-gray-900">Lightning Fast</h4>
                        <p class="mt-2 text-gray-600">High-speed internet with minimal latency for the best online experience.</p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="mt-4 text-lg font-semibold text-gray-900">99.9% Uptime</h4>
                        <p class="mt-2 text-gray-600">Reliable connection you can count on, with minimal downtime.</p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.5a9.5 9.5 0 11.001 19A9.5 9.5 0 0112 2.5z" />
                            </svg>
                        </div>
                        <h4 class="mt-4 text-lg font-semibold text-gray-900">24/7 Support</h4>
                        <p class="mt-2 text-gray-600">Round-the-clock customer support to help you whenever needed.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16" x-data="{ openFaq: null }">
            <div class="max-w-3xl mx-auto">
                <h3 class="text-2xl font-bold text-gray-900 text-center mb-8">Frequently Asked Questions</h3>

                <div class="space-y-4">
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-4 text-left focus:outline-none">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">How do I change my plan?</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 1" x-collapse class="px-6 pb-4">
                            <p class="text-gray-600">You can upgrade or change your plan anytime from your dashboard. Contact our support team for assistance with plan changes.</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg">
                        <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-4 text-left focus:outline-none">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">What payment methods do you accept?</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 2" x-collapse class="px-6 pb-4">
                            <p class="text-gray-600">We accept payments via wallet balance, Paystack, and Nomba. You can fund your wallet using bank transfers, cards, and other payment methods.</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg">
                        <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-4 text-left focus:outline-none">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Is there a setup fee?</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <div x-show="openFaq === 3" x-collapse class="px-6 pb-4">
                            <p class="text-gray-600">No, there are no setup fees. You only pay for your selected plan. Installation and setup are included at no extra cost.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Plans Available -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.034 0-3.9.785-5.291 2.069M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.5V11a6 6 0 00-5-5.917V5a2 2 0 00-4 0v.083A6 6 0 004 11v3.5c0 .398.158.78.439 1.061L6 17h4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Plans Available</h3>
                <p class="mt-2 text-gray-500">Currently, there are no active subscription plans available. Please check back later.</p>
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
