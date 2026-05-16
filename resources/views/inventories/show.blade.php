@extends('layouts.app')

@section('title', $inventory->name)
@section('page-title', 'Inventory: ' . $inventory->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div class="flex gap-4">
            <a href="{{ route('inventories.edit', $inventory) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" onclick="openRestockModal()">
                <i class="fas fa-plus-circle"></i> Restock
            </button>
        </div>
        <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <!-- Inventory Info Cards -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Item Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Item Name</p>
                    <p class="text-gray-800 font-medium">{{ $inventory->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Unit</p>
                    <p class="text-gray-800 font-medium">{{ $inventory->unit ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Unit Price</p>
                    <p class="text-gray-800 font-medium text-lg">₱{{ number_format($inventory->unit_price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Stock Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Current Stock</p>
                    <p class="text-gray-800 font-medium text-lg">{{ $inventory->quantity }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Reorder Level</p>
                    <p class="text-gray-800 font-medium">{{ $inventory->reorder_level }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total Value</p>
                    <p class="text-gray-800 font-medium text-lg">₱{{ number_format($inventory->quantity * $inventory->unit_price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Status & Dates</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <span class="badge 
                        @if($inventory->status === 'in_stock') bg-green-100 text-green-800
                        @elseif($inventory->status === 'low_stock') bg-yellow-100 text-yellow-800
                        @elseif($inventory->status === 'out_of_stock') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif px-3 py-1 rounded-full text-sm">
                        {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Expiry Date</p>
                    <p class="text-gray-800 font-medium">
                        @if($inventory->expiry_date)
                            {{ \Carbon\Carbon::parse($inventory->expiry_date)->format('M d, Y') }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Added On</p>
                    <p class="text-gray-800 font-medium text-sm">{{ $inventory->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($inventory->description)
    <div class="card bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Description</h3>
        <p class="text-blue-800">{{ $inventory->description }}</p>
    </div>
    @endif

    <!-- Stock Alert -->
    @if($inventory->quantity <= $inventory->min_quantity)
    <div class="card bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-500">
        <h3 class="text-sm font-semibold text-yellow-900 mb-2">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
        </h3>
        <p class="text-yellow-800">
            Current stock ({{ $inventory->quantity }}) is at or below minimum threshold ({{ $inventory->min_quantity }}). 
            <button type="button" onclick="openRestockModal()" class="text-yellow-700 font-semibold hover:underline">
                Restock now
            </button>
        </p>
    </div>
    @endif

    <!-- Recent Transactions -->
    @if($inventory->transactions->count() > 0)
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Transactions (Latest 10)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($inventory->transactions->take(10) as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($transaction->patient)
                                    <a href="{{ route('patients.show', $transaction->patient) }}" class="text-purple-600 hover:underline">
                                        {{ $transaction->patient->full_name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Restock Modal -->
<div id="restockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="card bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Restock Item</h3>
        <form action="{{ route('inventories.restock', $inventory) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Stock</label>
                <input type="number" value="{{ $inventory->quantity }}" disabled class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity to Add *</label>
                <input type="number" name="quantity" required min="1" value="{{ old('quantity') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            <div class="flex gap-4 justify-end">
                <button type="button" onclick="closeRestockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-save"></i> Restock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestockModal() {
    document.getElementById('restockModal').classList.remove('hidden');
}

function closeRestockModal() {
    document.getElementById('restockModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('restockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRestockModal();
    }
});
</script>
@endsection
