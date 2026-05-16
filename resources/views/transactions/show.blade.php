@extends('layouts.app')

@section('title', 'Transaction #' . $transaction->id)
@section('page-title', 'Transaction Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div class="flex gap-4">
            @if($transaction->status === 'pending')
                <form action="{{ route('transactions.update', $transaction) }}" method="POST" style="display:inline;">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check"></i> Mark Complete
                    </button>
                </form>
            @endif
            @if(in_array($transaction->status, ['pending', 'completed']))
                <button type="button" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700" onclick="openRefundModal()">
                    <i class="fas fa-undo"></i> Refund
                </button>
            @endif
        </div>
        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <!-- Transaction Info Cards -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Transaction Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Transaction ID</p>
                    <p class="text-gray-800 font-medium text-lg">#{{ $transaction->id }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Type</p>
                    <p class="text-gray-800 font-medium">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Amount</p>
                    <p class="text-gray-800 font-medium text-lg">₱{{ number_format($transaction->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <span class="badge 
                        @if($transaction->status === 'completed') bg-green-100 text-green-800
                        @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($transaction->status === 'refunded') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif px-3 py-1 rounded-full text-sm">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Related Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Patient</p>
                    <p class="text-gray-800 font-medium">
                        <a href="{{ route('patients.show', $transaction->patient) }}" class="text-purple-600 hover:underline">
                            {{ $transaction->patient->full_name }}
                        </a>
                    </p>
                </div>
                @if($transaction->appointment)
                <div>
                    <p class="text-xs text-gray-500 uppercase">Appointment</p>
                    <p class="text-gray-800 font-medium">
                        <a href="{{ route('appointments.show', $transaction->appointment) }}" class="text-purple-600 hover:underline">
                            {{ $transaction->appointment->appointment_date->format('M d, Y H:i') }}
                        </a>
                    </p>
                </div>
                @endif
                @if($transaction->inventory)
                <div>
                    <p class="text-xs text-gray-500 uppercase">Inventory Item</p>
                    <p class="text-gray-800 font-medium">
                        <a href="{{ route('inventories.show', $transaction->inventory) }}" class="text-purple-600 hover:underline">
                            {{ $transaction->inventory->item_name }}
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Dates</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Created</p>
                    <p class="text-gray-800 font-medium">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Updated</p>
                    <p class="text-gray-800 font-medium">{{ $transaction->updated_at->format('M d, Y H:i') }}</p>
                </div>
                @if($transaction->status === 'refunded')
                <div>
                    <p class="text-xs text-gray-500 uppercase">Refunded Amount</p>
                    <p class="text-gray-800 font-medium">₱{{ number_format($transaction->refund_amount ?? $transaction->amount, 2) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($transaction->notes)
    <div class="card bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Notes</h3>
        <p class="text-blue-800">{{ $transaction->notes }}</p>
    </div>
    @endif
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="card bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Process Refund</h3>
        <form action="{{ route('transactions.refund', $transaction) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Original Amount</label>
                <input type="text" value="₱{{ number_format($transaction->amount, 2) }}" disabled class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Amount ($) *</label>
                <input type="number" name="refund_amount" required step="0.01" min="0" max="{{ $transaction->amount }}" 
                       value="{{ old('refund_amount', $transaction->amount) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Reason *</label>
                <textarea name="refund_reason" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Enter reason for refund..."></textarea>
            </div>
            <div class="flex gap-4 justify-end">
                <button type="button" onclick="closeRefundModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    <i class="fas fa-check"></i> Process Refund
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRefundModal() {
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});
</script>
@endsection
