@extends('layouts.app')

@section('title', 'Reports Dashboard')
@section('page-title', 'Reports Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" action="{{ route('reports.dashboard') }}" class="flex gap-4 items-end">
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

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Income</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($totalIncome, 2) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Visits</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $dailyVisits->sum('visit_count') }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $lowStockItems }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Active Inventory</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $inventoryStatus->sum('count') }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Links -->
    <div class="grid grid-cols-3 gap-6">
        <a href="{{ route('reports.daily-visits') }}" class="bg-white rounded-lg shadow-sm p-8 hover:shadow-md transition text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Daily Patient Visits</h3>
            <p class="text-gray-600 text-sm mt-2">View patient visit statistics</p>
        </a>

        <a href="{{ route('reports.income') }}" class="bg-white rounded-lg shadow-sm p-8 hover:shadow-md transition text-center">
            <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Income Reports</h3>
            <p class="text-gray-600 text-sm mt-2">Financial performance analysis</p>
        </a>

        <a href="{{ route('reports.inventory') }}" class="bg-white rounded-lg shadow-sm p-8 hover:shadow-md transition text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-warehouse text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Inventory Status</h3>
            <p class="text-gray-600 text-sm mt-2">Stock levels and valuation</p>
        </a>

        <a href="{{ route('reports.doctor-performance') }}" class="bg-white rounded-lg shadow-sm p-8 hover:shadow-md transition text-center">
            <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-md text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Doctor Performance</h3>
            <p class="text-gray-600 text-sm mt-2">Appointment and revenue metrics</p>
        </a>

        <a href="{{ route('transactions.report') }}" class="bg-white rounded-lg shadow-sm p-8 hover:shadow-md transition text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-indigo-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Transaction Report</h3>
            <p class="text-gray-600 text-sm mt-2">Detailed transaction history</p>
        </a>
    </div>
</div>
@endsection
