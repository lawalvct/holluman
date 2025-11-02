@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Management</h1>
        <a href="{{ route('admin.admins.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i> Create Admin
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" x-data="{ show: true }" x-show="show">
        <div class="flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Admins</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Superadmins</p>
                    <p class="text-2xl font-bold">{{ $stats['superadmins'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-user-cog text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Regular Admins</p>
                    <p class="text-2xl font-bold">{{ $stats['regular'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <form method="GET" action="{{ route('admin.admins') }}" class="p-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                @if(request('search'))
                <a href="{{ route('admin.admins') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Admins Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($admins as $admin)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                <div class="text-sm text-gray-500">{{ $admin->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($admin->is_superadmin && $admin->id === 1)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-crown mr-1"></i> Superadmin
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Admin
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @php
                                $permissions = $admin->permissions ?? [];
                                $allPermissions = \App\Models\User::getAllPermissions();
                            @endphp
                            @if($admin->is_superadmin && $admin->id === 1)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">All Permissions</span>
                            @elseif(empty($permissions))
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">No permissions</span>
                            @else
                                @foreach($permissions as $permission)
                                    @if(isset($allPermissions[$permission]))
                                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800" title="{{ $allPermissions[$permission] }}">
                                            {{ $allPermissions[$permission] }}
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $admin->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($admin->is_superadmin && $admin->id === 1)
                            <span class="text-gray-400 italic">Protected</span>
                        @else
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        @if(request('search'))
                            No admins found matching "{{ request('search') }}"
                        @else
                            No admins found
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($admins->hasPages())
    <div class="mt-6">
        {{ $admins->links() }}
    </div>
    @endif
</div>
@endsection
