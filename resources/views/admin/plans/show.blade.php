@extends('layouts.admin')

@section('title', 'View Plan: ' . $plan->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.plans.index') }}" class="text-blue-600 hover:text-blue-900">&larr; Back to Plans</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">{{ $plan->name }}</h1>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Plan Details</h3>
                    <dl class="mt-4 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $plan->description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">₦{{ number_format($plan->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $plan->duration_in_days }} days</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data Limit</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $plan->data_limit_in_gb }} GB</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Speed Limit</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $plan->speed_limit_in_mbps }} Mbps</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($plan->is_active)
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $plan->sort_order }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
