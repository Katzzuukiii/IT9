@extends('layouts.app')

@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient: ' . $patient->full_name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card bg-white rounded-lg p-8">
        <form action="{{ route('patients.update', $patient) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">First Name </label>
                    <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name </label>
                    <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email </label>
                    <input type="email" name="email" value="{{ old('email', $patient->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('email') border-red-500 @enderror">
                    @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone </label>
                    <input type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('phone') border-red-500 @enderror">
                    @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth *</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('date_of_birth') border-red-500 @enderror">
                    @error('date_of_birth') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Age *</label>
                    <input type="number" name="age" value="{{ old('age', $patient->age) }}" required min="0" max="150"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('age') border-red-500 @enderror">
                    @error('age') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
                    <select name="gender"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>



            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address   </label>
                <input type="text" name="address" value="{{ old('address', $patient->address) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('address') border-red-500 @enderror">
                @error('address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Blood Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Blood Type</label>
                <select name="bloodType"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('bloodType') border-red-500 @enderror">
                    <option value="">Select Blood Type</option>
                    <option value="A+" {{ old('bloodType', $patient->bloodType) === 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('bloodType', $patient->bloodType) === 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('bloodType', $patient->bloodType) === 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('bloodType', $patient->bloodType) === 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('bloodType', $patient->bloodType) === 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('bloodType', $patient->bloodType) === 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('bloodType', $patient->bloodType) === 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('bloodType', $patient->bloodType) === 'O-' ? 'selected' : '' }}>O-</option>
                </select>
                @error('bloodType') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Medical History</label>
                <textarea name="medical_history" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('medical_history', $patient->medical_history) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Allergies</label>
                <textarea name="allergies" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('allergies', $patient->allergies) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                <select name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="active" {{ old('status', $patient->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $patient->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blocked" {{ old('status', $patient->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn-primary px-8 py-2 text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save"></i> Update Patient
                </button>
                <a href="{{ route('patients.show', $patient) }}" class="px-8 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
