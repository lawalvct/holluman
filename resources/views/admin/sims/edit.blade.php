@extends('layouts.admin')
@section('title', 'Edit SIM')
@section('content')
<div class="container mx-auto py-6 max-w-xl">
    <h1 class="text-2xl font-bold mb-4">Edit SIM</h1>
    <form method="POST" action="{{ route('admin.sims.update', $sim) }}" class="bg-white rounded shadow p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block font-semibold mb-1">SIM Number</label>
            <input type="text" name="sim_number" value="{{ old('sim_number', $sim->sim_number) }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring @error('sim_number') border-red-500 @enderror">
            @error('sim_number')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Camera Name</label>
            <input type="text" name="camera_name" value="{{ old('camera_name', $sim->camera_name) }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring @error('camera_name') border-red-500 @enderror">
            @error('camera_name')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Camera Location</label>
            <input type="text" name="camera_location" value="{{ old('camera_location', $sim->camera_location) }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring @error('camera_location') border-red-500 @enderror">
            @error('camera_location')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="flex gap-4 mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update SIM</button>
            <a href="{{ route('admin.sims') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>
@endsection
