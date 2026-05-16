<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('patients', 'email')->ignore($this->patient->id)->whereNull('deleted_at')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('patients', 'phone')->ignore($this->patient->id)->whereNull('deleted_at')],
            'age' => 'required|integer|min:0|max:150',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'required|string|max:255',
            'bloodType' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,blocked',
        ];
    }
}
