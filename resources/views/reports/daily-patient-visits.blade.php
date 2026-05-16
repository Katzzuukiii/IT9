@extends('layouts.app')

@section('title', 'Daily Patient Visits Report')
@section('page-title', 'Daily Patient Visits Report')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" action="{{ route('reports.daily-visits') }}" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 font-semibold">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <p class="text-gray-600">Total Patient Visits: <span class="text-3xl font-bold text-gray-900">{{ $totalVisits }}</span></p>
    </div>

    <!-- Daily Summary -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daily Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Visits</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($dailySummary as $day)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($day->visit_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">
                                    {{ $day->visit_count }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                No data available for the selected period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Appointments -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Appointment Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $appointment->start_time->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                    {{ $appointment->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $appointment->doctor->full_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $appointment->room->room_number }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                    Completed
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No appointments found for the selected period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection
