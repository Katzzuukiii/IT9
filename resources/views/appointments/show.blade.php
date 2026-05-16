@extends('layouts.app')

@section('title', 'Appointment Details')
@section('page-title', 'Appointment Details')

@section('content')
<div class="space-y-6">
    <!-- Action Buttons -->
    <div class="flex justify-between items-start">
        <div class="flex gap-2">
            @if($appointment->status === 'scheduled')
                <a href="{{ route('appointments.edit', $appointment) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('appointments.complete', $appointment) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check"></i> Mark Complete
                    </button>
                </form>
            @endif

            @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="cancellation_reason" value="User cancelled">
                    <button type="submit" onclick="return confirm('Are you sure you want to cancel this appointment?');"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            @endif
        </div>

        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <!-- Main Info Cards -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Patient</h3>
            <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:text-purple-900 font-semibold text-lg">
                {{ $appointment->patient->full_name }}
            </a>
            <p class="text-gray-600 text-sm mt-2">
                <i class="fas fa-envelope"></i> {{ $appointment->patient->email }}
            </p>
            <p class="text-gray-600 text-sm">
                <i class="fas fa-phone"></i> {{ $appointment->patient->phone }}
            </p>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Doctor</h3>
            @if($appointment->doctor)
                <a href="{{ route('doctors.show', $appointment->doctor) }}" class="text-purple-600 hover:text-purple-900 font-semibold text-lg">
                    {{ $appointment->doctor->full_name }}
                </a>
                <p class="text-gray-600 text-sm mt-2">
                    <i class="fas fa-stethoscope"></i> {{ $appointment->doctor->specialization }}
                </p>
                <p class="text-gray-600 text-sm">
                    Rate: ₱{{ number_format($appointment->doctor->hourly_rate, 2) }}/hr
                </p>
            @else
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <i class="fas fa-exclamation-circle text-amber-600"></i>
                        <p class="text-amber-800 font-semibold">No Doctor Assigned</p>
                    </div>
                    <a href="{{ route('appointments.edit', $appointment) }}" class="inline-block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-user-md"></i> Assign Doctor
                    </a>
                </div>
            @endif
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Room</h3>
            <p class="text-purple-600 hover:text-purple-900 font-semibold text-lg">
                {{ $appointment->room->name }}
            </p>
            <p class="text-gray-600 text-sm mt-2">
                <i class="fas fa-door-open"></i> Type: {{ $appointment->room->type }}
            </p>
            <p class="text-gray-600 text-sm">
                Rate: ₱{{ number_format($appointment->room->hourly_rate, 2) }}/hr
            </p>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="grid grid-cols-2 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Appointment Details</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Date & Time</p>
                    <p class="text-gray-800 font-medium">{{ $appointment->start_time->format('l, M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">End Time</p>
                    <p class="text-gray-800 font-medium">{{ $appointment->end_time->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Duration</p>
                    <p class="text-gray-800 font-medium">{{ $appointment->duration_in_minutes }} minutes</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <span class="badge-status 
                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                        @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Reason</p>
                    <p class="text-gray-800 font-medium">{{ $appointment->reason ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Fee Breakdown</h3>
            <div class="space-y-3">
                @if($appointment->doctor)
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-600">Doctor Fee ({{ $appointment->duration_in_hours }} hrs @ ₱{{ number_format($appointment->doctor->hourly_rate, 2) }}/hr):</span>
                    <span class="font-medium">₱{{ number_format($appointment->consultation_fee ?? 0, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-600">Room Fee ({{ $appointment->duration_in_hours }} hrs @ ₱{{ number_format($appointment->room->hourly_rate, 2) }}/hr):</span>
                    <span class="font-medium">₱{{ number_format($appointment->calculateRoomFee(), 2) }}</span>
                </div>

                @if($appointment->transactions()->where('status', 'completed')->count() > 0)
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-600">Additional Services:</span>
                    <span class="font-medium">₱{{ number_format($appointment->transactions()->where('status', 'completed')->sum('amount'), 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between pt-2 text-lg font-bold">
                    <span>Total:</span>
                    <span class="text-purple-600">₱{{ number_format($appointment->total_fee ?? $appointment->calculateTotalFee(), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    @if($appointment->notes || $appointment->diagnosis || $appointment->treatment_plan)
    <div class="grid grid-cols-2 gap-6">
        @if($appointment->notes)
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Notes</h3>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $appointment->notes }}</p>
        </div>
        @endif

        @if($appointment->diagnosis)
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Diagnosis</h3>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $appointment->diagnosis }}</p>
        </div>
        @endif
    </div>

    @if($appointment->treatment_plan)
    <div class="card bg-white p-6 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-600 mb-4">Treatment Plan</h3>
        <p class="text-gray-700 text-sm leading-relaxed">{{ $appointment->treatment_plan }}</p>
    </div>
    @endif
    @endif

    <!-- Transactions -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Related Transactions</h3>
            <a href="{{ route('transactions.create') }}?appointment_id={{ $appointment->id }}" class="text-purple-600 hover:text-purple-900 text-sm">
                <i class="fas fa-plus"></i> Add Transaction
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Qty</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ ucfirst($transaction->type) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($transaction->quantity, 2) }}</td>
                            <td class=\"px-6 py-4 text-sm text-gray-800 font-medium\">\u20b1{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('transactions.show', $transaction) }}" class="text-purple-600 hover:text-purple-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p>No transactions found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
