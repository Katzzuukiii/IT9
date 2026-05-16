@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointments Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div class="flex gap-4 flex-1">
            <form action="{{ route('appointments.search') }}" method="GET" class="flex gap-2 flex-1">
                <input type="text" name="q" placeholder="Search by patient or doctor name..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
        <a href="{{ route('appointments.create') }}" class="btn-primary px-6 py-2 text-white rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus"></i> Schedule Appointment
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg flex gap-4">
        <select onchange="if(this.value) window.location.href = '?status=' + this.value" class="px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">All Statuses</option>
            <option value="scheduled">Scheduled</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Appointments Table -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Appointments List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Duration</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="{{ !$appointment->doctor_id ? 'bg-amber-50' : '' }}">
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $appointment->patient->full_name }}</td>
                            <td class="px-6 py-4 text-sm {{ !$appointment->doctor_id ? 'text-amber-600 font-semibold' : 'text-gray-600' }}">
                                @if($appointment->doctor)
                                    {{ $appointment->doctor->full_name }}
                                @else
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>No Doctor Assigned</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->room->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->formatted_start_time }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->duration_in_minutes }} min</td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">₱{{ number_format($appointment->total_fee ?? 0, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm flex gap-2">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-purple-600 hover:text-purple-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$appointment->doctor_id)
                                    <a href="{{ route('appointments.edit', $appointment) }}" class="text-blue-600 hover:text-blue-900" title="Assign Doctor">
                                        <i class="fas fa-user-md"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>No appointments found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection
