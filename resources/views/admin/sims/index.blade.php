@extends('layouts.admin')
@section('title', 'User SIMs')
@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">User SIMs</h1>
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SIM number, camera, or user..." class="border rounded px-3 py-2 focus:outline-none focus:ring w-64">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
    </form>
    <div class="mb-4 flex gap-6">
        <div>Total SIMs: <span class="font-semibold">{{ $stats['total_sims'] }}</span></div>
        <div>Unique Users: <span class="font-semibold">{{ $stats['unique_users'] }}</span></div>
    </div>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">SIM Number</th>
                    <th class="px-4 py-2 border">Camera Name</th>
                    <th class="px-4 py-2 border">Camera Location</th>
                    <th class="px-4 py-2 border">User</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sims as $sim)
                <tr>
                    <td class="px-4 py-2 border">{{ $sim->id }}</td>
                    <td class="px-4 py-2 border">{{ $sim->sim_number }}</td>
                    <td class="px-4 py-2 border">{{ $sim->camera_name }}</td>
                    <td class="px-4 py-2 border">{{ $sim->camera_location }}</td>
                    <td class="px-4 py-2 border">
                        @if($sim->user)
                            <a href="{{ route('admin.users.show', $sim->user) }}" class="text-blue-600 hover:underline">{{ $sim->user->name }}</a>
                        @else
                            <span class="text-gray-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border">
                        <a href="{{ route('admin.sims.show', $sim) }}" class="text-blue-600 hover:underline mr-2">View</a>
                        <a href="{{ route('admin.sims.edit', $sim) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
                        <form action="{{ route('admin.sims.destroy', $sim) }}" method="POST" class="inline" onsubmit="return confirm('Delete this SIM?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 border text-center text-gray-500">No SIMs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sims->links() }}</div>
</div>
@endsection
