<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'image_url' => $this->imageUrl(),
            'is_veg' => $this->is_veg,
            'is_available' => $this->is_available,
            'is_popular' => $this->is_popular,
            'is_bestseller' => $this->is_bestseller,
            'is_special' => $this->is_special,
            'sort_order' => $this->sort_order,
        ];
    }
}
