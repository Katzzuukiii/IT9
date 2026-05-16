@extends('layouts.app')

@section('title', $doctor->full_name)
@section('page-title', $doctor->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div class="flex gap-4">
            <a href="{{ route('doctors.edit', $doctor) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
        <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <!-- Doctor Info Cards -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Professional Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Full Name</p>
                    <p class="text-gray-800 font-medium">{{ $doctor->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">License Number</p>
                    <p class="text-gray-800 font-medium">{{ $doctor->license_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Specialization</p>
                    <p class="text-gray-800 font-medium">
                        <span class="badge bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                            {{ $doctor->specialization }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Experience</p>
                    <p class="text-gray-800 font-medium">{{ $doctor->experience_years }} years</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Hourly Rate</p>
                    <p class="text-gray-800 font-medium text-lg">₱{{ number_format($doctor->hourly_rate, 2) }}/hr</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <p class="text-gray-800 font-medium break-all">{{ $doctor->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Phone</p>
                    <p class="text-gray-800 font-medium">{{ $doctor->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <span class="badge 
                        @if($doctor->status === 'active') bg-green-100 text-green-800
                        @elseif($doctor->status === 'on_leave') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif px-3 py-1 rounded-full text-sm">
                        {{ ucfirst($doctor->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Statistics</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total Appointments</p>
                    <p class="text-gray-800 font-medium text-lg">{{ $doctor->appointments->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Assigned Rooms</p>
                    <p class="text-gray-800 font-medium text-lg">{{ $doctor->rooms->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Member Since</p>
                    <p class="text-gray-800 font-medium">{{ $doctor->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Rooms -->
    @if($doctor->rooms->count() > 0)
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Assigned Rooms ({{ $doctor->rooms->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room Number</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Capacity</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Equipment</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($doctor->rooms as $room)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $room->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $room->capacity }} people</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if(is_array($room->equipment))
                                    {{ implode(', ', array_slice($room->equipment, 0, 2)) }}{{ count($room->equipment) > 2 ? '...' : '' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-status {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Appointments -->
    @if($doctor->appointments->count() > 0)
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Appointments (Latest 5)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($doctor->appointments->take(5) as $appointment)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $appointment->start_time->format('M d, Y') }} @ {{ $appointment->start_time->format('h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->room->room_number }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($appointment->consultation_fee, 2) }}</td>
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

    @if($doctor->notes)
    <div class="card bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Notes</h3>
        <p class="text-blue-800">{{ $doctor->notes }}</p>
    </div>
    @endif
</div>
@endsection
