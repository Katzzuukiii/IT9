@extends('layouts.app')

@section('title', $staff->name)
@section('page-title', 'Staff Member: ' . $staff->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $staff->name }}</h1>
                <p class="text-gray-600 mt-1">{{ ucfirst($staff->role) }} Account</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('staff.edit', $staff) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @if(auth()->id() !== $staff->id)
                    <form action="{{ route('staff.destroy', $staff) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('staff.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8">
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 text-sm">Name</p>
                        <p class="text-gray-900 font-semibold">{{ $staff->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Email</p>
                        <p class="text-gray-900 font-semibold">{{ $staff->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Phone</p>
                        <p class="text-gray-900 font-semibold">{{ $staff->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Address</p>
                        <p class="text-gray-900 font-semibold">{{ $staff->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 text-sm">Role</p>
                        <p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($staff->role === 'admin') bg-red-100 text-red-800
                                @elseif($staff->role === 'doctor') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($staff->role) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($staff->isActive()) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($staff->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Member Since</p>
                        <p class="text-gray-900 font-semibold">{{ $staff->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Last Updated</p>
                        <p class="text-gray-900 font-semibold">{{ $staff->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
