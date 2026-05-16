@extends('layouts.app')

@section('title', 'Rooms')
@section('page-title', 'Rooms Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form action="{{ route('rooms.search') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="q" placeholder="Search rooms..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <a href="{{ route('rooms.create') }}" class="btn-primary px-6 py-2 text-white rounded-lg hover:shadow-lg">
            <i class="fas fa-plus"></i> Add Room
        </a>
    </div>

    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Rooms List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room Number</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Capacity</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hourly Rate</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rooms as $room)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $room->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $room->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $room->type ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $room->capacity }} people</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($room->hourly_rate, 2) }}/hr</td>
                            <td class="px-6 py-4">
                                <span class="badge-status {{ $room->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $room->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('rooms.show', $room) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('rooms.edit', $room) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No rooms found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
@endsection
