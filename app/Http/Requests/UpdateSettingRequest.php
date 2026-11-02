<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_name' => ['sometimes', 'required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'about' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'opening_hours' => ['nullable', 'string', 'max:200'],
            'whatsapp_number' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9]{10,15}$/'],
            'upi_id' => ['nullable', 'string', 'max:80'],
            'upi_name' => ['nullable', 'string', 'max:80'],
            'delivery_charge' => ['nullable', 'numeric', 'min:0'],
            'free_delivery_min' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:40'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'currency' => ['nullable', 'string', 'max:10'],
            'hero_image' => ['nullable', 'url'],
        ];
    }
}
