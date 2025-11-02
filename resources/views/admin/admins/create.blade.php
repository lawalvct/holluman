@extends('layouts.admin')

@section('title', 'Create Admin')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.admins') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Admins
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Create New Admin</h1>
        <p class="text-gray-600 mt-2">Create a new admin user and assign permissions</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.admins.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
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
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Permissions -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Permissions <span class="text-red-500">*</span>
                </label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(\App\Models\User::getAllPermissions() as $key => $label)
                        <label class="flex items-center p-3 bg-white rounded border border-gray-200 hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                   {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}
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
                    <i class="fas fa-user-plus mr-2"></i> Create Admin
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
