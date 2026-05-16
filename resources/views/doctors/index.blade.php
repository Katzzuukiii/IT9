@extends('layouts.app')

@section('title', 'Doctors')
@section('page-title', 'Doctors Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form action="{{ route('doctors.search') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="q" placeholder="Search doctors..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <a href="{{ route('doctors.create') }}" class="btn-primary px-6 py-2 text-white rounded-lg hover:shadow-lg">
            <i class="fas fa-plus"></i> Add Doctor
        </a>
    </div>

    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Doctors List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Specialization</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">License</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hourly Rate</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Experience</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $doctor->full_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->specialization }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->license_number }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">₱{{ number_format($doctor->hourly_rate, 2) }}/hr</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->experience_years }} yrs</td>
                            <td class="px-6 py-4">
                                <span class="badge-status {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($doctor->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('doctors.show', $doctor) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('doctors.edit', $doctor) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('doctors.edit', $doctor) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No doctors found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $doctors->links() }}
        </div>
    </div>
</div>
@endsection
