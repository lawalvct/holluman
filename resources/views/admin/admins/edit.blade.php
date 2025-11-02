@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.admins') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Admins
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Admin</h1>
        <p class="text-gray-600 mt-2">Update admin details and permissions</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.admins.update', $admin) }}">
            @csrf
            @method('PUT')

            <!-- Admin Info Card -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">{{ $admin->name }}</p>
                        <p class="text-sm text-gray-600">{{ $admin->email }}</p>
                        <p class="text-xs text-gray-500 mt-1">Member since {{ $admin->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    New Password (Optional)
                </label>
                <input type="password" id="password" name="password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password. Minimum 8 characters if changing.</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm New Password
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Permissions -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Permissions <span class="text-red-500">*</span>
                </label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $currentPermissions = old('permissions', $admin->permissions ?? []);
                        @endphp
                        @foreach(\App\Models\User::getAllPermissions() as $key => $label)
                        <label class="flex items-center p-3 bg-white rounded border border-gray-200 hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                   {{ in_array($key, $currentPermissions) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i> Select at least one permission. Admin will only see menu items they have access to.
                </p>
            </div>

            <!-- Select All / Deselect All -->
            <div class="mb-6">
                <button type="button" onclick="selectAllPermissions()" class="text-blue-600 hover:text-blue-800 text-sm mr-4">
                    <i class="fas fa-check-square mr-1"></i> Select All
                </button>
                <button type="button" onclick="deselectAllPermissions()" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-square mr-1"></i> Deselect All
                </button>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.admins') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Update Admin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection
