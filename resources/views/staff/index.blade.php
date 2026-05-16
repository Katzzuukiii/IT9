@extends('layouts.app')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Staff Members</h2>
            <p class="text-gray-600 mt-1">Manage clinic staff and administrators</p>
        </div>
        <a href="{{ route('staff.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 font-semibold">
            <i class="fas fa-plus mr-2"></i> Add Staff Member
        </a>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('staff.index') }}" class="flex gap-2">
        <input type="text" name="search" placeholder="Search by name, email, or phone..." 
               value="{{ request('search') }}"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
        <button type="submit" class="bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg font-semibold">
            <i class="fas fa-search"></i>
        </button>
    </form>

    <!-- Staff Table -->
    <div class="bg-white rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Phone</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Role</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($staff as $member)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $member->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $member->phone ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($member->role === 'admin') bg-red-100 text-red-800
                                    @elseif($member->role === 'doctor') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($member->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($member->isActive()) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('staff.show', $member) }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('staff.edit', $member) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(auth()->id() !== $member->id)
                                    <form action="{{ route('staff.destroy', $member) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p class="font-semibold">No staff members found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $staff->links() }}
    </div>
</div>
@endsection
