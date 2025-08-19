@extends('layouts.app')

@section('title', 'My SIMs')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">My SIMs</h2>
        <a href="{{ route('sims.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Add SIM
        </a>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if($sims->isEmpty())
        <div class="text-gray-500">No SIMs found.</div>
    @else
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SIM Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Camera Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Camera Location</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sims as $sim)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $sim->sim_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $sim->camera_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $sim->camera_location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('sims.edit', $sim) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                                <form action="{{ route('sims.destroy', $sim) }}" method="POST" class="inline" onsubmit="return confirm('Delete this SIM?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
