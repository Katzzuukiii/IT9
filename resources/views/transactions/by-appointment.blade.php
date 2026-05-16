@extends('layouts.app')

@section('title', 'Appointment Transactions')
@section('page-title', 'Appointment Transactions')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form action="{{ route('transactions.search') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="q" placeholder="Search transactions..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('transactions.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            <i class="fas fa-list"></i> All Transactions
        </a>
    </div>

    @if($appointment)
    <!-- Appointment Details -->
    <div class="card bg-white rounded-lg p-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Related Appointment</h3>
                <div class="grid grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Patient</p>
                        <p class="text-gray-800 font-medium">
                            <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Doctor</p>
                        <p class="text-gray-800 font-medium">
                            <a href="{{ route('doctors.show', $appointment->doctor) }}" class="text-purple-600 hover:underline">
                                {{ $appointment->doctor->full_name }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Date & Time</p>
                        <p class="text-gray-800 font-medium">{{ $appointment->start_time->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Fee</p>
                        <p class="text-gray-800 font-medium text-lg">₱{{ number_format($appointment->consultation_fee, 2) }}</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('appointments.show', $appointment) }}" class="text-purple-600 hover:underline">
                <i class="fas fa-arrow-right"></i> View Appointment
            </a>
        </div>
    </div>
    @endif

    <!-- Transactions for Appointment -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">#{{ $transaction->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
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
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No transactions found for this appointment</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary -->
    @if($transactions->count() > 0)
    <div class="grid grid-cols-4 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Total Transactions</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $transactions->count() }}</p>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Total Amount</h3>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($transactions->sum('amount'), 2) }}</p>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Completed</h3>
            <p class="text-3xl font-bold text-blue-600">
                ₱{{ number_format($transactions->where('status', 'completed')->sum('amount'), 2) }}
            </p>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">Pending</h3>
            <p class="text-3xl font-bold text-yellow-600">
                ₱{{ number_format($transactions->where('status', 'pending')->sum('amount'), 2) }}
            </p>
        </div>
    </div>
    @endif
</div>
@endsection
