<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date_format:Y-m-d H:i|after:now',
            'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,confirmed',
        ];
    }

    public function prepareForValidation()
    {
        // Convert datetime-local format to standard format for storage
        if ($this->has('start_time')) {
            $this->merge([
                'start_time' => str_replace('T', ' ', $this->start_time),
            ]);
        }
        if ($this->has('end_time')) {
            $this->merge([
                'end_time' => str_replace('T', ' ', $this->end_time),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'start_time.after' => 'Appointment time must be in the future.',
            'end_time.after' => 'End time must be after start time.',
        ];
    }
}
