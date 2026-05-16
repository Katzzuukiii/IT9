<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'inventory_id' => 'nullable|exists:inventories,id',
            'type' => 'required|in:consultation,service,inventory,room_fee,medication,payment',
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01|max:99999.99',
            'unit_price' => 'required|numeric|min:0|max:99999.99',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'status' => 'required|in:pending,completed',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
