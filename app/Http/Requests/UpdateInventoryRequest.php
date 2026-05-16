<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'item_code' => ['required', 'string', Rule::unique('inventories', 'item_code')->ignore($this->inventory->id)->whereNull('deleted_at')],
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0|max:99999.99',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'supplier' => 'nullable|string|max:255',
        ];
    }
}
