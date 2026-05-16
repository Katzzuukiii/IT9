@extends('layouts.app')

@section('title', 'Inventory Status Report')
@section('page-title', 'Inventory Status Report')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Items</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $summary['total_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Active Items</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">{{ $summary['active_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Low Stock</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $summary['low_stock_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Expired Items</p>
                    <p class="text-2xl font-bold text-red-600 mt-2">{{ $summary['expired_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Value</p>
                    <p class="text-2xl font-bold text-purple-600 mt-2">₱{{ number_format($summary['total_value'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Status Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Items</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total Quantity</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($statusBreakdown as $status)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-semibold">
                                <span class="px-3 py-1 rounded-full text-xs
                                    @if($status->status === 'active') bg-green-100 text-green-800
                                    @elseif($status->status === 'low_stock') bg-yellow-100 text-yellow-800
                                    @elseif($status->status === 'expired') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                {{ $status->count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                {{ $status->total_quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                ₱{{ number_format($status->total_value ?? 0, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No inventory data available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Inventory Items -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Inventory Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Item Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Unit Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventories as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                {{ $item->item_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->category }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                ₱{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($item->status === 'active') bg-green-100 text-green-800
                                    @elseif($item->status === 'low_stock') bg-yellow-100 text-yellow-800
                                    @elseif($item->status === 'expired') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                ₱{{ number_format($item->quantity * $item->unit_price, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No inventory items available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
