@extends('layouts.app')

@section('title', $room->name)
@section('page-title', 'Room: ' . $room->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div class="flex gap-4">
            <a href="{{ route('rooms.edit', $room) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
        <form action="{{ route('rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <!-- Room Info Cards -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Room Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Room Name</p>
                    <p class="text-gray-800 font-medium text-lg">{{ $room->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Room Number</p>
                    <p class="text-gray-800 font-medium">{{ $room->room_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Type</p>
                    <p class="text-gray-800 font-medium">{{ $room->type ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Capacity</p>
                    <p class="text-gray-800 font-medium">{{ $room->capacity }} people</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Hourly Rate</p>
                    <p class="text-gray-800 font-medium text-lg">₱{{ number_format($room->hourly_rate, 2) }}/hr</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <span class="badge 
                        @if($room->is_available) bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif px-3 py-1 rounded-full text-sm">
                        {{ $room->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg col-span-2">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Equipment</h3>
            <div class="flex flex-wrap gap-2">
                @if(is_array($room->equipment))
                    @foreach($room->equipment as $item)
                        <span class="badge bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-tools"></i> {{ $item }}
                        </span>
                    @endforeach
                @else
                    <p class="text-gray-600">No equipment listed</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($room->description)
    <div class="card bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Description</h3>
        <p class="text-blue-800">{{ $room->description }}</p>
    </div>
    @endif

    <!-- Assigned Doctors -->
    @if($room->doctors->count() > 0)
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Assigned Doctors ({{ $room->doctors->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Specialization</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">License Number</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hourly Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($room->doctors as $doctor)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <a href="{{ route('doctors.show', $doctor) }}" class="text-purple-600 hover:underline">
                                    {{ $doctor->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->specialization }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->license_number }}</td>
                            <td class=\"px-6 py-4 text-sm font-medium text-gray-800\">\u20b1{{ number_format($doctor->hourly_rate, 2) }}/hr</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Appointments in this Room -->
    @if($room->appointments->count() > 0)
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Appointments (Latest 5)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($room->appointments->take(5) as $appointment)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <a href="{{ route('doctors.show', $appointment->doctor) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->doctor->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $appointment->start_time->format('M d, Y') }} @ {{ $appointment->start_time->format('h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                                    @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
