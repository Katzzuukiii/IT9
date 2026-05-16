@extends('layouts.app')

@section('title', 'Add Doctor')
@section('page-title', 'Add New Doctor')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card bg-white rounded-lg p-8">
        <form action="{{ route('doctors.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">License Number *</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('license_number') border-red-500 @enderror"
                           placeholder="e.g., MD-2024-001">
                    @error('license_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Specialization *</label>
                    <select name="specialization" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('specialization') border-red-500 @enderror">
                        <option value="">Select Specialization</option>
                        <option value="Cardiology" {{ old('specialization') === 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
                        <option value="Neurology" {{ old('specialization') === 'Neurology' ? 'selected' : '' }}>Neurology</option>
                        <option value="Dermatology" {{ old('specialization') === 'Dermatology' ? 'selected' : '' }}>Dermatology</option>
                        <option value="Pediatrics" {{ old('specialization') === 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
                        <option value="Orthopedics" {{ old('specialization') === 'Orthopedics' ? 'selected' : '' }}>Orthopedics</option>
                        <option value="General Practice" {{ old('specialization') === 'General Practice' ? 'selected' : '' }}>General Practice</option>
                        <option value="Psychiatry" {{ old('specialization') === 'Psychiatry' ? 'selected' : '' }}>Psychiatry</option>
                        <option value="Oncology" {{ old('specialization') === 'Oncology' ? 'selected' : '' }}>Oncology</option>
                    </select>
                    @error('specialization') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Years of Experience</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years') }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hourly Rate</label>
                    <input type="number" name="hourly_rate" value="{{ old('hourly_rate') }}" required step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('hourly_rate') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('hourly_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-4 justify-end">
                <a href="{{ route('doctors.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-save"></i> Save Doctor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
