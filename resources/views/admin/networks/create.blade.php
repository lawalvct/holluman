@extends('layouts.admin')

@section('title', 'Add New Network')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Network</h1>
            <p class="text-gray-600 mt-2">Create a new ISP network provider</p>
        </div>
        <a href="{{ route('admin.networks') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Networks
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <form method="POST" action="{{ route('admin.networks.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Network Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., MTN">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Network Code *</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., MTN" maxlength="10">
                    @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="n3tdata_plainid" class="block text-sm font-medium text-gray-700 mb-2">N3tdata Plan ID</label>
                    <input type="text" id="n3tdata_plainid" name="n3tdata_plainid" value="{{ old('n3tdata_plainid') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., 1, 2, 3, 4">
                    {{-- <p class="text-sm text-gray-500 mt-1">N3tdata API network identifier (e.g., 1=MTN, 2=Airtel, 3=Glo, 4=9Mobile)</p> --}}
                    @error('n3tdata_plainid')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Mobile Telephone Network">
                    @error('full_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div> --}}

                {{-- <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Network Type *</label>
                    <select id="type" name="type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Type</option>
                        <option selected value="mobile" {{ old('type') === 'mobile' ? 'selected' : '' }}>Mobile</option>
                        <option value="broadband" {{ old('type') === 'broadband' ? 'selected' : '' }}>Broadband</option>
                        <option value="fiber" {{ old('type') === 'fiber' ? 'selected' : '' }}>Fiber</option>
                        <option value="satellite" {{ old('type') === 'satellite' ? 'selected' : '' }}>Satellite</option>
                    </select>
                    @error('type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div> --}}

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Network description...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Visual Information -->
                <div class="md:col-span-2 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Visual Information</h3>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Network Logo</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Upload network logo (JPG, PNG, GIF - Max: 2MB)</p>
                    @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Brand Color</label>
                    <input type="color" id="color" name="color" value="{{ old('color', '#000000') }}"
                           class="w-full h-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('color')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Coverage Information -->
                {{-- <div class="md:col-span-2 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Coverage Information</h3>
                </div> --}}

                {{-- <div>
                    <label for="coverage_percentage" class="block text-sm font-medium text-gray-700 mb-2">Coverage Percentage</label>
                    <input type="number" id="coverage_percentage" name="coverage_percentage"
                           value="{{ old('coverage_percentage') }}" min="0" max="100" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., 85.5">
                    @error('coverage_percentage')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div> --}}

                {{-- <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('sort_order')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div> --}}

                {{-- <div class="md:col-span-2">
                    <label for="service_areas" class="block text-sm font-medium text-gray-700 mb-2">Service Areas</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @php
                        $nigerianStates = [
                            'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
                            'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe',
                            'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara',
                            'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau',
                            'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
                        ];
                        @endphp
                        @foreach($nigerianStates as $state)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="service_areas[]" value="{{ $state }}"
                                       {{ in_array($state, old('service_areas', [])) ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">{{ $state }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('service_areas')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div> --}}

                <!-- Contact Information -->
                {{-- <div class="md:col-span-2 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                </div> --}}

                {{-- <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="text" id="contact_phone" name="contact_info[phone]"
                           value="{{ old('contact_info.phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., +234 123 456 7890">
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_info[email]"
                           value="{{ old('contact_info.email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., support@network.com">
                </div>

                <div class="md:col-span-2">
                    <label for="contact_website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" id="contact_website" name="contact_info[website]"
                           value="{{ old('contact_info.website') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., https://www.network.com">
                </div>
            </div> --}}

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Create Network
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
