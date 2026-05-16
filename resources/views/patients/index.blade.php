@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', 'Patients Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div class="flex gap-4 flex-1">
            <form action="{{ route('patients.search') }}" method="GET" class="flex gap-2 flex-1">
                <input type="text" name="q" placeholder="Search by name, email, or phone..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
        <a href="{{ route('patients.create') }}" class="btn-primary px-6 py-2 text-white rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus"></i> Add Patient
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4">
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Patients</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Patient::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Active Patients</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Patient::active()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Inactive Patients</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Patient::where('status', 'inactive')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Blocked</p>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Patient::where('status', 'blocked')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Patient List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date of Birth</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $patient->full_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->date_of_birth->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="badge-status 
                                    @if($patient->status === 'active') bg-green-100 text-green-800
                                    @elseif($patient->status === 'inactive') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($patient->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('patients.show', $patient) }}" class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>No patients found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection
