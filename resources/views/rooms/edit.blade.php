@extends('layouts.app')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room: ' . $room->room_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card bg-white rounded-lg p-8">
        <form action="{{ route('rooms.update', $room) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Name *</label>
                    <input type="text" name="name" value="{{ old('name', $room->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('name') border-red-500 @enderror"
                           placeholder="e.g., General Ward">
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Number *</label>
                    <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('room_number') border-red-500 @enderror"
                           placeholder="e.g., 101, ICU-1, OR-A">
                    @error('room_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Type</label>
                    <select name="type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Type</option>
                        <option value="Consultation" {{ old('type', $room->type) === 'Consultation' ? 'selected' : '' }}>Consultation</option>
                        <option value="Surgery" {{ old('type', $room->type) === 'Surgery' ? 'selected' : '' }}>Surgery</option>
                        <option value="Recovery" {{ old('type', $room->type) === 'Recovery' ? 'selected' : '' }}>Recovery</option>
                        <option value="ICU" {{ old('type', $room->type) === 'ICU' ? 'selected' : '' }}>ICU</option>
                        <option value="General Ward" {{ old('type', $room->type) === 'General Ward' ? 'selected' : '' }}>General Ward</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Capacity (People) *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" required min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('capacity') border-red-500 @enderror">
                    @error('capacity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hourly Rate ($) *</label>
                    <input type="number" name="hourly_rate" value="{{ old('hourly_rate', $room->hourly_rate) }}" required step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('hourly_rate') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('hourly_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="flex items-center text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $room->is_available) ? 'checked' : '' }}
                               class="rounded border-gray-300 mr-2">
                        Available
                    </label>
                    @error('is_available') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Equipment *</label>
                <p class="text-xs text-gray-600 mb-3">Enter equipment names separated by commas</p>
                <textarea name="equipment" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('equipment') border-red-500 @enderror"
                          placeholder="Equipment list...">{{ old('equipment', is_array($room->equipment) ? implode(', ', $room->equipment) : $room->equipment) }}</textarea>
                @error('equipment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('description', $room->description) }}</textarea>
            </div>

            <div class="flex gap-4 justify-end">
                <a href="{{ route('rooms.show', $room) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-save"></i> Update Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
