<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('doctors', 'email')->whereNull('deleted_at')],
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => ['required', 'string', Rule::unique('doctors', 'license_number')->whereNull('deleted_at')],
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

    public function messages(): array
    {
        return [
            'license_number.unique' => 'This license number is already registered.',
            'email.unique' => 'This email is already registered.',
            'hourly_rate.required' => 'Hourly rate is required.',
        ];
    }
}
