@extends('layouts.app')

@section('title', 'Transaction Report')
@section('page-title', 'Financial Report & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Report Controls -->
    <div class="card bg-white p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Report Filters</h3>
        <form action="{{ route('transactions.report') }}" method="GET" class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-5 gap-4">
        <div class="card bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border-l-4 border-blue-500">
            <h4 class="text-xs font-semibold text-blue-600 uppercase mb-2">Total Transactions</h4>
            <p class="text-3xl font-bold text-blue-900">{{ $transactions->count() }}</p>
        </div>
        <div class="card bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border-l-4 border-green-500">
            <h4 class="text-xs font-semibold text-green-600 uppercase mb-2">Total Revenue</h4>
            <p class="text-3xl font-bold text-green-900">₱{{ number_format($transactions->sum('amount'), 2) }}</p>
        </div>
        <div class="card bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg border-l-4 border-yellow-500">
            <h4 class="text-xs font-semibold text-yellow-600 uppercase mb-2">Pending Amount</h4>
            <p class="text-3xl font-bold text-yellow-900">₱{{ number_format($transactions->where('status', 'pending')->sum('amount'), 2) }}</p>
        </div>
        <div class="card bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-lg border-l-4 border-red-500">
            <h4 class="text-xs font-semibold text-red-600 uppercase mb-2">Refunded</h4>
            <p class="text-3xl font-bold text-red-900">₱{{ number_format($transactions->where('status', 'refunded')->sum('amount'), 2) }}</p>
        </div>
        <div class="card bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border-l-4 border-purple-500">
            <h4 class="text-xs font-semibold text-purple-600 uppercase mb-2">Avg Transaction</h4>
            <p class="text-3xl font-bold text-purple-900">₱{{ number_format($transactions->avg('amount') ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Breakdown by Type -->
    <div class="grid grid-cols-2 gap-6">
        <div class="card bg-white rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">By Transaction Type</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Count</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $byType = $transactions->groupBy('type');
                        @endphp
                        @forelse($byType as $type => $items)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $items->count() }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">₱{{ number_format($items->sum('amount'), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card bg-white rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">By Status</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Count</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $byStatus = $transactions->groupBy('status');
                        @endphp
                        @forelse($byStatus as $status => $items)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <span class="badge 
                                        @if($status === 'completed') bg-green-100 text-green-800
                                        @elseif($status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($status === 'refunded') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif px-3 py-1 rounded-full text-xs">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $items->count() }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">₱{{ number_format($items->sum('amount'), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Transaction Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">#{{ $transaction->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->patient->full_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">₱{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800
                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($transaction->status === 'refunded') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
