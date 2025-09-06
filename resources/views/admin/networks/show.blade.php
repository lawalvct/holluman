@extends('layouts.admin')

@section('title', 'Network Details - ' . $network->name)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Network Details</h1>
            <p class="text-gray-600 mt-2">View network information and details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.networks.edit', $network) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-edit mr-2"></i>Edit Network
            </a>
            <a href="{{ route('admin.networks') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Networks
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-6">
                    @if($network->image)
                        <img src="{{ $network->image_url }}" alt="{{ $network->name }}"
                             class="h-16 w-16 rounded-lg object-cover mr-4">
                    @else
                        <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                            <i class="fas fa-network-wired text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $network->name }}</h2>
                        @if($network->full_name)
                            <p class="text-gray-600">{{ $network->full_name }}</p>
                        @endif
                        <div class="flex items-center mt-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mr-2
                                {{ $network->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $network->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($network->type === 'mobile') bg-blue-100 text-blue-800
                                @elseif($network->type === 'broadband') bg-green-100 text-green-800
                                @elseif($network->type === 'fiber') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($network->type) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($network->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $network->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Basic Information</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Network Code</dt>
                                <dd class="text-sm text-gray-900">{{ $network->code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">N3tdata Plan ID</dt>
                                <dd class="text-sm text-gray-900">{{ $network->n3tdata_plainid ?? 'Not set' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Network Type</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($network->type) }}</dd>
                            </div>
                            {{-- <div>
                                <dt class="text-sm font-medium text-gray-500">Coverage</dt>
                                <dd class="text-sm text-gray-900">{{ $network->coverage_display }}</dd>
                            </div> --}}
                            @if($network->color)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Brand Color</dt>
                                    <dd class="text-sm text-gray-900 flex items-center">
                                        <div class="w-4 h-4 rounded mr-2" style="background-color: {{ $network->color }}"></div>
                                        {{ $network->color }}
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                                <dd class="text-sm text-gray-900">{{ $network->sort_order }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($network->contact_info)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Contact Information</h3>
                            <dl class="space-y-2">
                                @if(isset($network->contact_info['phone']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="text-sm text-gray-900">{{ $network->contact_info['phone'] }}</dd>
                                    </div>
                                @endif
                                @if(isset($network->contact_info['email']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="text-sm text-gray-900">{{ $network->contact_info['email'] }}</dd>
                                    </div>
                                @endif
                                @if(isset($network->contact_info['website']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Website</dt>
                                        <dd class="text-sm text-gray-900">
                                            <a href="{{ $network->contact_info['website'] }}" target="_blank"
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ $network->contact_info['website'] }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif
                </div>
            </div>

            @if($network->service_areas && count($network->service_areas) > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Service Areas</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($network->service_areas as $area)
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                {{ $area }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.networks.toggle-status', $network) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full {{ $network->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas {{ $network->is_active ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                            {{ $network->is_active ? 'Deactivate' : 'Activate' }} Network
                        </button>
                    </form>

                    <a href="{{ route('admin.networks.edit', $network) }}"
                       class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium text-center block">
                        <i class="fas fa-edit mr-2"></i>Edit Network
                    </a>

                    <form method="POST" action="{{ route('admin.networks.destroy', $network) }}"
                          onsubmit="return confirm('Are you sure you want to delete this network? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-trash mr-2"></i>Delete Network
                        </button>
                    </form>
                </div>
            </div>

            <!-- Network Statistics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Network Statistics</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="text-sm text-gray-900">{{ $network->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Updated</dt>
                        <dd class="text-sm text-gray-900">{{ $network->updated_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $network->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $network->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
