<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('rooms', 'name')->whereNull('deleted_at')],
            'room_number' => 'required|string|max:50',
            'type' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1|max:100',
            'equipment' => 'nullable|string',
            'description' => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'hourly_rate' => 'required|numeric|min:0|max:99999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Room name already exists.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        // Ensure is_available is a boolean
        $data['is_available'] = $this->has('is_available') ? true : false;
        return $data;
    }
}
