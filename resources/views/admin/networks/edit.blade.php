@extends('layouts.admin')

@section('title', 'Edit Network - ' . $network->name)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Network</h1>
            <p class="text-gray-600 mt-2">Update network provider information</p>
        </div>
        <a href="{{ route('admin.networks') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Networks
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <form method="POST" action="{{ route('admin.networks.update', $network) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Network Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $network->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., MTN">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Network Code *</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $network->code) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., MTN">
                    @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $network->full_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Mobile Telephone Networks Nigeria">
                    @error('full_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Network Type *</label>
                    <select id="type" name="type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Type</option>
                        <option value="mobile" {{ old('type', $network->type) === 'mobile' ? 'selected' : '' }}>Mobile</option>
                        <option value="broadband" {{ old('type', $network->type) === 'broadband' ? 'selected' : '' }}>Broadband</option>
                        <option value="fiber" {{ old('type', $network->type) === 'fiber' ? 'selected' : '' }}>Fiber</option>
                        <option value="satellite" {{ old('type', $network->type) === 'satellite' ? 'selected' : '' }}>Satellite</option>
                    </select>
                    @error('type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Network description...">{{ old('description', $network->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Visual & Branding -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Visual & Branding</h3>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Network Logo</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @if($network->image)
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Current logo:</p>
                            <img src="{{ $network->image_url }}" alt="{{ $network->name }}" class="h-16 w-16 object-cover rounded-lg mt-1">
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                    @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Brand Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" id="color" name="color" value="{{ old('color', $network->color ?: '#000000') }}"
                               class="w-16 h-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" placeholder="#000000" value="{{ old('color', $network->color) }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               onchange="document.getElementById('color').value = this.value">
                    </div>
                    @error('color')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Coverage & Service -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Coverage & Service</h3>
                </div>

                <div>
                    <label for="coverage_percentage" class="block text-sm font-medium text-gray-700 mb-2">Coverage Percentage</label>
                    <div class="relative">
                        <input type="number" id="coverage_percentage" name="coverage_percentage"
                               value="{{ old('coverage_percentage', $network->coverage_percentage) }}"
                               min="0" max="100" step="0.1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="85.5">
                        <span class="absolute right-3 top-2 text-gray-500">%</span>
                    </div>
                    @error('coverage_percentage')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', $network->sort_order) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first in listings</p>
                    @error('sort_order')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- <div class="md:col-span-2">
                    <label for="service_areas" class="block text-sm font-medium text-gray-700 mb-2">Service Areas</label>
                    <input type="text" id="service_areas_input"
                           value="{{ old('service_areas_string', is_array($network->service_areas) ? implode(', ', $network->service_areas) : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Lagos, Abuja, Kano, Port Harcourt">
                    <input type="hidden" id="service_areas" name="service_areas">
                    <p class="text-xs text-gray-500 mt-1">Enter states/cities separated by commas</p>
                    @error('service_areas')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div> --}}

                <!-- Contact Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Contact Information</h3>
                </div>

                <div>
                    <label for="customer_service" class="block text-sm font-medium text-gray-700 mb-2">Customer Service</label>
                    <input type="text" id="customer_service"
                           value="{{ old('customer_service', optional($network->contact_info)['customer_service'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="180 or +234-XXX-XXX-XXXX">
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" id="website"
                           value="{{ old('website', optional($network->contact_info)['website'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="https://www.example.com">
                </div>

                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email"
                           value="{{ old('email', optional($network->contact_info)['email'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="customercare@example.com">
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Status</h3>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $network->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Network is active</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.networks') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Update Network
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle service areas conversion
    const serviceAreasInput = document.getElementById('service_areas_input');
    const serviceAreasHidden = document.getElementById('service_areas');

    function updateServiceAreas() {
        const areas = serviceAreasInput.value.split(',').map(area => area.trim()).filter(area => area);
        serviceAreasHidden.value = JSON.stringify(areas);
    }

    serviceAreasInput.addEventListener('input', updateServiceAreas);
    updateServiceAreas(); // Initialize on page load

    // Handle contact info
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const contactInfo = {
            customer_service: document.getElementById('customer_service').value,
            website: document.getElementById('website').value,
            email: document.getElementById('email').value
        };

        // Remove empty values
        Object.keys(contactInfo).forEach(key => {
            if (!contactInfo[key]) delete contactInfo[key];
        });

        // Create hidden input for contact_info
        const contactInfoInput = document.createElement('input');
        contactInfoInput.type = 'hidden';
        contactInfoInput.name = 'contact_info';
        contactInfoInput.value = JSON.stringify(contactInfo);
        form.appendChild(contactInfoInput);
    });

    // Handle color picker sync
    const colorPicker = document.getElementById('color');
    const colorInput = colorPicker.nextElementSibling;

    colorPicker.addEventListener('change', function() {
        colorInput.value = this.value;
    });

    colorInput.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorPicker.value = this.value;
        }
    });
});
</script>
@endsection
