<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $appointment = $this->route('appointment');
        
        return [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date_format:Y-m-d H:i|after_or_equal:now',
            'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
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
            'start_time.after_or_equal' => 'Appointment time must be in the present or future.',
            'end_time.after' => 'End time must be after start time.',
        ];
    }
}
