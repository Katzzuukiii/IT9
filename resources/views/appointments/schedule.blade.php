@extends('layouts.app')

@section('title', 'Appointment Schedule')
@section('page-title', 'View All Appointments')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form action="{{ route('appointments.schedule') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="q" placeholder="Search appointments..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <a href="{{ route('appointments.create') }}" class="btn-primary px-6 py-2 text-white rounded-lg hover:shadow-lg">
            <i class="fas fa-plus"></i> New Appointment
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Appointment::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Scheduled</p>
                    <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Appointment::where('status', 'scheduled')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Completed</p>
                    <p class="text-3xl font-bold text-green-600">{{ \App\Models\Appointment::where('status', 'completed')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Cancelled</p>
                    <p class="text-3xl font-bold text-red-600">{{ \App\Models\Appointment::where('status', 'cancelled')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">All Upcoming Appointments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $appointment->formatted_start_date }} @ {{ $appointment->formatted_start_time_only }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <a href="{{ route('doctors.show', $appointment->doctor) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->doctor->full_name }}
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
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('appointments.edit', $appointment) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
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
</div>
@endsection
