@extends('layouts.app')

@section('title', 'Inventory')
@section('page-title', 'Inventory Management')

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-4 gap-4">
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Items</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Inventory::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">In Stock</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Inventory::where('status', 'in_stock')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Low Stock</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Inventory::lowStock()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Expired</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Inventory::expired()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center">
        <form action="{{ route('inventories.search') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="q" placeholder="Search inventory..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="flex gap-2">
            <a href="{{ route('inventories.low-stock') }}" class="px-4 py-2 border border-yellow-300 text-yellow-700 rounded-lg hover:bg-yellow-50">
                <i class="fas fa-exclamation-triangle"></i> Low Stock
            </a>
            <a href="{{ route('inventories.create') }}" class="btn-primary px-6 py-2 text-white rounded-lg hover:shadow-lg">
                <i class="fas fa-plus"></i> Add Item
            </a>
        </div>
    </div>

    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Inventory Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Item Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Min Qty</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Value</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventories as $inventory)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $inventory->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $inventory->quantity }} {{ $inventory->unit ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $inventory->reorder_level }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($inventory->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($inventory->quantity * $inventory->unit_price, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($inventory->status === 'in_stock') bg-green-100 text-green-800
                                    @elseif($inventory->status === 'low_stock') bg-yellow-100 text-yellow-800
                                    @elseif($inventory->status === 'out_of_stock') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('inventories.show', $inventory) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventories.edit', $inventory) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No inventory items found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inventories->links() }}
        </div>
    </div>
</div>
@endsection
