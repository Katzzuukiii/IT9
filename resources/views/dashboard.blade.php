@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
   

    <!-- Key Metrics -->
    <div class="grid grid-cols-4 gap-6">
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Patients</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ \App\Models\Patient::count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ \App\Models\Patient::active()->count() }} active</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Appointments</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ \App\Models\Appointment::count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ \App\Models\Appointment::where('status', 'scheduled')->count() }} scheduled</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Doctors</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ \App\Models\Doctor::count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ \App\Models\Doctor::where('status', 'active')->count() }} active</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-md text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ \App\Models\Inventory::lowStock()->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ \App\Models\Inventory::count() }} total items</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <!-- Recent Appointments -->
        <div class="card bg-white rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Recent Appointments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse(\App\Models\Appointment::latest()->take(5)->get() as $appointment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <a href="{{ route('patients.show', $appointment->patient) }}" class="text-purple-600 hover:underline">
                                        {{ $appointment->patient->full_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->doctor->full_name ?? 'No Doctor Assigned' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->start_time->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No appointments yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue Overview -->
        {{-- <div class="card bg-white rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Financial Summary</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600">Total Transactions</span>
                    <span class="text-2xl font-bold text-gray-800">{{ \App\Models\Transaction::count() }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600">Total Revenue</span>
                    <span class="text-2xl font-bold text-green-600">${{ number_format(\App\Models\Transaction::where('status', 'completed')->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600">Pending Payments</span>
                    <span class="text-2xl font-bold text-yellow-600">${{ number_format(\App\Models\Transaction::where('status', 'pending')->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Refunded Amount</span>
                    <span class="text-2xl font-bold text-red-600">${{ number_format(\App\Models\Transaction::where('status', 'refunded')->sum('amount'), 2) }}</span>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Quick Actions -->
    <div class="grid grid-cols-4 gap-4">
        <a href="{{ route('patients.create') }}" class="card bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
            <h3 class="font-semibold text-gray-800">Add Patient</h3>
        </a>
        <a href="{{ route('appointments.create') }}" class="card bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-calendar-plus text-green-600 text-2xl mb-2"></i>
            <h3 class="font-semibold text-gray-800">New Appointment</h3>
        </a>
        <a href="{{ route('inventories.create') }}" class="card bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-box-plus text-purple-600 text-2xl mb-2"></i>
            <h3 class="font-semibold text-gray-800">Add Inventory</h3>
        </a>
        <a href="{{ route('transactions.create') }}" class="card bg-gradient-to-br from-pink-50 to-pink-100 p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <i class="fas fa-plus-circle text-pink-600 text-2xl mb-2"></i>
            <h3 class="font-semibold text-gray-800">New Transaction</h3>
        </a>
    </div>
</div>
@endsection
