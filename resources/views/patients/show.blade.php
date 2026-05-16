@extends('layouts.app')

@section('title', $patient->full_name)
@section('page-title', $patient->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div class="flex gap-4">
            <a href="{{ route('patients.edit', $patient) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
        <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <!-- Patient Info Cards -->
    <div class="grid grid-cols-3 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Personal Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Full Name</p>
                    <p class="text-gray-800 font-medium">{{ $patient->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Age</p>
                    <p class="text-gray-800 font-medium">{{ $patient->age }} years</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Gender</p>
                    <p class="text-gray-800 font-medium">{{ $patient->gender ? ucfirst($patient->gender) : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Date of Birth</p>
                    <p class="text-gray-800 font-medium">{{ $patient->date_of_birth->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <span class="badge-status 
                        @if($patient->status === 'active') bg-green-100 text-green-800
                        @elseif($patient->status === 'inactive') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($patient->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <p class="text-gray-800 font-medium break-all">{{ $patient->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Phone</p>
                    <p class="text-gray-800 font-medium">{{ $patient->phone }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Address</p>
                    <p class="text-gray-800 font-medium">{{ $patient->address }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Emergency Contact</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Contact Name</p>
                    <p class="text-gray-800 font-medium">{{ $patient->emergency_contact_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Contact Phone</p>
                    <p class="text-gray-800 font-medium">{{ $patient->emergency_contact_phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History & Allergies -->
    @if($patient->medical_history || $patient->allergies)
    <div class="grid grid-cols-2 gap-6">
        @if($patient->medical_history)
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Medical History</h3>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $patient->medical_history }}</p>
        </div>
        @endif

        @if($patient->allergies)
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Allergies</h3>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $patient->allergies }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Appointments Section -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Appointments</h3>
            <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="text-purple-600 hover:text-purple-900 text-sm">
                <i class="fas fa-plus"></i> Schedule Appointment
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $appointment->doctor->full_name ?? 'No Doctor Assigned' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->room->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->start_time->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">₱{{ number_format($appointment->total_fee ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-purple-600 hover:text-purple-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p>No appointments found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appointments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>

    <!-- Transactions Section -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Transaction History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ ucfirst($transaction->type) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">₱{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800
                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
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
