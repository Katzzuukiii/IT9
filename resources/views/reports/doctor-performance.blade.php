@extends('layouts.app')

@section('title', 'Doctor Performance Report')
@section('page-title', 'Doctor Performance Report')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" action="{{ route('reports.doctor-performance') }}" class="flex gap-4 items-end">
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

    <!-- Doctor Performance Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Doctor Performance Metrics</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Specialization</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Completed Appointments</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total Revenue</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Average Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($doctors as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('doctors.show', $item['doctor']) }}" class="text-purple-600 hover:underline font-semibold">
                                    {{ $item['doctor']->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item['doctor']->specialization }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">
                                    {{ $item['completed_appointments'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600 text-right">
                                ₱{{ number_format($item['total_revenue'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                ₱{{ number_format($item['average_revenue'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No doctor data available for the selected period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
