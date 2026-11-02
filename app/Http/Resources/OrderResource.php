<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_name' => $this->customer_name,
            'customer_mobile' => $this->customer_mobile,
            'customer_email' => $this->customer_email,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'delivery_address' => $this->delivery_address,
            'landmark' => $this->landmark,
            'preferred_time' => $this->preferred_time,
            'instructions' => $this->instructions,
            'subtotal' => $this->subtotal,
            'delivery_charge' => $this->delivery_charge,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'grand_total' => $this->grand_total,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
