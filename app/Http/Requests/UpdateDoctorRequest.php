<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('doctors', 'email')->ignore($this->doctor->id)->whereNull('deleted_at')],
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => ['required', 'string', Rule::unique('doctors', 'license_number')->ignore($this->doctor->id)->whereNull('deleted_at')],
            'hourly_rate' => 'required|numeric|min:0|max:99999.99',
            'bio' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience_years' => 'required|integer|min:0|max:70',
            'status' => 'required|in:active,inactive,on_leave',
            'shift_start' => 'nullable|date_format:H:i',
            'shift_end' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'rooms' => 'nullable|array',
            'rooms.*' => 'exists:rooms,id',
        ];
    }
}
