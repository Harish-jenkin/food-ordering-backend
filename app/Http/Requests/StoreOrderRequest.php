<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.food_item_id' => ['required', 'integer', 'exists:food_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_mobile' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'order_type' => ['required', 'in:delivery,takeaway'],
            'delivery_address' => ['required_if:order_type,delivery', 'nullable', 'string', 'max:500'],
            'landmark' => ['nullable', 'string', 'max:200'],
            'preferred_time' => ['nullable', 'string', 'max:80'],
            'instructions' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cash,upi,card'],
            'idempotency_key' => ['nullable', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_address.required_if' => 'A delivery address is required for home delivery.',
        ];
    }
}
