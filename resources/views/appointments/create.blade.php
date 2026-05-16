@extends('layouts.app')

@section('title', 'Schedule Appointment')
@section('page-title', 'Schedule New Appointment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card bg-white rounded-lg p-8">
        <p class="text-sm text-gray-600 mb-6">
            <i class="fas fa-info-circle"></i> The system will automatically prevent double bookings and schedule conflicts for doctors and rooms.
        </p>

        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Patient *</label>
                    <select name="patient_id" id="patient" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('patient_id') border-red-500 @enderror">
                        <option value="">Select Patient</option>
                        @foreach(\App\Models\Patient::active()->get() as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Doctor *</label>
                    <select name="doctor_id" id="doctor" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('doctor_id') border-red-500 @enderror">
                        <option value="">Select Doctor</option>
                        @foreach(\App\Models\Doctor::active()->get() as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->full_name }} ({{ $doctor->specialization }})
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room *</label>
                    <select name="room_id" id="room" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('room_id') border-red-500 @enderror">
                        <option value="">Select Room</option>
                        @foreach(\App\Models\Room::available()->get() as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} ({{ $room->type }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reason</label>
                    <input type="text" name="reason" value="{{ old('reason') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date & Time *</label>
                    <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('start_time') border-red-500 @enderror">
                    @error('start_time') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date & Time *</label>
                    <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('end_time') border-red-500 @enderror">
                    @error('end_time') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                </select>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn-primary px-8 py-2 text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-calendar-check"></i> Schedule Appointment
                </button>
                <a href="{{ route('appointments.index') }}" class="px-8 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
