@extends('layouts.app')

@section('title', 'Expired Items')
@section('page-title', 'Expired Inventory Items')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form action="{{ route('inventories.search') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="q" placeholder="Search items..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="flex gap-2">
            <a href="{{ route('inventories.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-list"></i> All Items
            </a>
        </div>
    </div>

    <!-- Alert -->
    <div class="card bg-red-50 border-l-4 border-red-500 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-red-900 mb-2">
            <i class="fas fa-times-circle"></i> Expired Items Alert
        </h3>
        <p class="text-red-800">
            These items have expired and should be removed from inventory immediately to maintain compliance and safety standards.
        </p>
    </div>

    <!-- Expired Items Table -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                Expired Items ({{ $inventories->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Item Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Days Expired</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Current Stock</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Value</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventories as $inventory)
                        <tr class="hover:bg-red-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $inventory->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="badge bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold">
                                    {{ $inventory->expiry_date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-red-600">
                                {{ now()->diffInDays($inventory->expiry_date) }} days
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $inventory->quantity }} {{ $inventory->unit ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">₱{{ number_format($inventory->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                ₱{{ number_format($inventory->quantity * $inventory->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('inventories.show', $inventory) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Remove this expired item?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                                <p>No expired items in inventory.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary -->
    @if($inventories->count() > 0)
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Summary</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Expired Items</p>
                    <p class="text-2xl font-bold text-red-600">{{ $inventories->count() }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Total Expired Units</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Units to Remove</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $inventories->sum('quantity') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Total Loss Value</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total Loss</p>
                    <p class="text-2xl font-bold text-red-600">
                        ₱{{ number_format($inventories->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
