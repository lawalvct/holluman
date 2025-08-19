@extends('layouts.app')

@section('title', 'Add SIM')

@section('content')
<div class="max-w-md mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Add New SIM</h2>
    <form action="{{ route('sims.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">SIM Number</label>
            <input type="text" name="sim_number" value="{{ old('sim_number') }}" required class="w-full border border-gray-400 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('sim_number')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Camera Name</label>
            <input type="text" name="camera_name" value="{{ old('camera_name') }}" required class="w-full border border-gray-400 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('camera_name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Camera Location</label>
            <input type="text" name="camera_location" value="{{ old('camera_location') }}" required class="w-full border border-gray-400 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('camera_location')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-between">
            <a href="{{ route('sims') }}" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add SIM</button>
        </div>
    </form>
</div>
@endsection
