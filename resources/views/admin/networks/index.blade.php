@extends('layouts.admin')

@section('title', 'Networks Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Networks Management</h1>
            <p class="text-gray-600 mt-2">Manage ISP network providers and their details</p>
        </div>
        <a href="{{ route('admin.networks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-plus mr-2"></i>Add Network
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-network-wired text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Networks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_networks'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Networks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_networks'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-mobile-alt text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Mobile Networks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['mobile_networks'] }}</p>
                </div>
            </div>
        </div>


    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.networks') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search networks..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select id="type" name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="mobile" {{ request('type') === 'mobile' ? 'selected' : '' }}>Mobile</option>
                        <option value="broadband" {{ request('type') === 'broadband' ? 'selected' : '' }}>Broadband</option>
                        <option value="fiber" {{ request('type') === 'fiber' ? 'selected' : '' }}>Fiber</option>
                        <option value="satellite" {{ request('type') === 'satellite' ? 'selected' : '' }}>Satellite</option>
                    </select>
                </div> --}}

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Networks Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Network</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N3tdata ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage</th> --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($networks as $network)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($network->image)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $network->image_url }}" alt="{{ $network->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-network-wired text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $network->name }}</div>
                                        {{-- <div class="text-sm text-gray-500">{{ $network->full_name ?: 'N/A' }}</div> --}}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $network->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $network->n3tdata_plainid ?? '-' }}
                                </div>
                                @if($network->n3tdata_plainid)
                                    <div class="text-xs text-gray-500">API ID</div>
                                @else
                                    <div class="text-xs text-red-500">Not set</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($network->type === 'mobile') bg-blue-100 text-blue-800
                                    @elseif($network->type === 'broadband') bg-green-100 text-green-800
                                    @elseif($network->type === 'fiber') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($network->type) }}
                                </span>
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $network->coverage_display }}
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $network->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $network->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.networks.show', $network) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md transition-colors"
                                       title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.networks.edit', $network) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-md transition-colors"
                                       title="Edit Network">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <!-- Toggle Status Button -->
                                    <form method="POST" action="{{ route('admin.networks.toggle-status', $network) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 {{ $network->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-md transition-colors"
                                                onclick="return confirm('Are you sure you want to {{ $network->is_active ? 'deactivate' : 'activate' }} this network?')"
                                                title="{{ $network->is_active ? 'Deactivate' : 'Activate' }} Network">
                                            <i class="fas {{ $network->is_active ? 'fa-ban' : 'fa-check' }} text-sm"></i>
                                        </button>
                                    </form>

                                    <!-- Delete Button -->
                                    {{-- <form method="POST" action="{{ route('admin.networks.destroy', $network) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-md transition-colors"
                                                onclick="return confirm('Are you sure you want to delete this network? This action cannot be undone.')"
                                                title="Delete Network">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No networks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $networks->links() }}
        </div>
    </div>
</div>
@endsection
