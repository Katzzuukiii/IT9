@extends('layouts.app')

@section('title', 'Add Patient')
@section('page-title', 'Add New Patient')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card bg-white rounded-lg p-8">
        <form action="{{ route('patients.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('email') border-red-500 @enderror">
                    @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('phone') border-red-500 @enderror">
                    @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth *</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('date_of_birth') border-red-500 @enderror">
                    @error('date_of_birth') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Age -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Age *</label>
                    <input type="number" name="age" value="{{ old('age') }}" required min="0" max="150"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('age') border-red-500 @enderror">
                    @error('age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
                    <select name="gender"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>



            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address *</label>
                <input type="text" name="address" value="{{ old('address') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('address') border-red-500 @enderror">
                @error('address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Blood Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Blood Type</label>
                <select name="bloodType"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('bloodType') border-red-500 @enderror">
                    <option value="">Select Blood Type</option>
                    <option value="A+" {{ old('bloodType') === 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('bloodType') === 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('bloodType') === 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('bloodType') === 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('bloodType') === 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('bloodType') === 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('bloodType') === 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('bloodType') === 'O-' ? 'selected' : '' }}>O-</option>
                </select>
                @error('bloodType') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Medical History -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Medical History</label>
                <textarea name="medical_history" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('medical_history') border-red-500 @enderror">{{ old('medical_history') }}</textarea>
                @error('medical_history') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Allergies -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Allergies</label>
                <textarea name="allergies" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('allergies') border-red-500 @enderror">{{ old('allergies') }}</textarea>
                @error('allergies') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Emergency Contact -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('status') border-red-500 @enderror">
                    <option value="">Select Status</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
                @error('status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">Create Patient</button>
                <a href="{{ route('patients.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
