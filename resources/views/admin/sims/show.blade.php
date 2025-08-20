@extends('layouts.admin')
@section('title', 'SIM Details')
@section('content')
<div class="container mx-auto py-6 max-w-xl">
    <h1 class="text-2xl font-bold mb-4">SIM Details</h1>
    <div class="bg-white rounded shadow p-6">
        <div class="mb-2"><span class="font-semibold">SIM Number:</span> {{ $sim->sim_number }}</div>
        <div class="mb-2"><span class="font-semibold">Camera Name:</span> {{ $sim->camera_name }}</div>
        <div class="mb-2"><span class="font-semibold">Camera Location:</span> {{ $sim->camera_location }}</div>
        <div class="mb-2"><span class="font-semibold">User:</span>
            @if($sim->user)
                <a href="{{ route('admin.users.show', $sim->user) }}" class="text-blue-600 hover:underline">{{ $sim->user->name }}</a>
            @else
                <span class="text-gray-500">N/A</span>
            @endif
        </div>
        <div class="flex gap-4 mt-6">
            <a href="{{ route('admin.sims.edit', $sim) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
            <form action="{{ route('admin.sims.destroy', $sim) }}" method="POST" onsubmit="return confirm('Delete this SIM?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
            </form>
            <a href="{{ route('admin.sims') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back</a>
        </div>
    </div>
</div>
@endsection
