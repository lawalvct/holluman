@extends('layouts.admin')

@section('title', 'User SIMs')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User SIMs Management</h1>
            <p class="text-gray-600 mt-2">Manage all user SIM cards and their camera assignments</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-sim-card text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total SIMs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sims'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Unique Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['unique_users'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.sims') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search by SIM number, camera, or user..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SIMs Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SIM Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Camera Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Camera Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sims as $sim)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $sim->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono">{{ $sim->sim_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $sim->camera_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $sim->camera_location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sim->user)
                                    <a href="{{ route('admin.users.show', $sim->user) }}" class="text-blue-600 hover:underline">{{ $sim->user->name }}</a>
                                @else
                                    <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.sims.show', $sim) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md transition-colors"
                                       title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.sims.edit', $sim) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-md transition-colors"
                                       title="Edit SIM">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.sims.destroy', $sim) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-md transition-colors"
                                                onclick="return confirm('Delete this SIM?');"
                                                title="Delete SIM">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No SIMs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sims->links() }}
        </div>
    </div>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mt-6">{{ session('success') }}</div>
    @endif
</div>
@endsection
