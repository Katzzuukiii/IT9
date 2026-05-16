@extends('layouts.app')

@section('title', $doctor->full_name . ' Schedule')
@section('page-title', $doctor->full_name . ' - Schedule')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <a href="{{ route('doctors.show', $doctor) }}" class="text-purple-600 hover:underline text-sm mb-2">
                <i class="fas fa-arrow-left"></i> Back to Doctor
            </a>
            <h1 class="text-2xl font-bold text-gray-800">{{ $doctor->full_name }}'s Schedule</h1>
            <p class="text-gray-600 text-sm">{{ $doctor->specialization }} | {{ $doctor->license_number }}</p>
        </div>
    </div>

    <!-- Doctor Info Card -->
    <div class="card bg-white p-6 rounded-lg">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Doctor Information</h3>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Specialization</p>
                        <p class="text-gray-800 font-medium">{{ $doctor->specialization }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Hourly Rate</p>
                        <p class="text-gray-800 font-medium text-lg">₱{{ number_format($doctor->hourly_rate, 2) }}/hr</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Status</p>
                        <span class="badge {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} px-3 py-1 rounded-full text-sm">
                            {{ ucfirst($doctor->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule by Date -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Appointments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $appointment->start_time->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->start_time->format('h:i A') }} - {{ $appointment->end_time->format('h:i A') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->room->room_number }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($appointment->consultation_fee, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="badge
                                    @if($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                                    @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                    px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                                <p>No upcoming appointments scheduled</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($appointments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Appointment Statistics</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total Appointments</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $doctor->appointments->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Completed</p>
                    <p class="text-lg font-semibold text-green-600">{{ $doctor->appointments->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Upcoming Schedule</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Scheduled Ahead</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $doctor->appointments->where('status', 'scheduled')->where('start_time', '>=', now())->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Total Earnings</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Consultation Fees</p>
                    <p class="text-2xl font-bold text-green-600">
                        ₱{{ number_format($doctor->appointments->sum('consultation_fee'), 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
