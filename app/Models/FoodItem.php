<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_veg',
        'is_available',
        'is_popular',
        'is_bestseller',
        'is_special',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_veg' => 'boolean',
            'is_available' => 'boolean',
            'is_popular' => 'boolean',
            'is_bestseller' => 'boolean',
            'is_special' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FoodItem $item) {
            if (blank($item->slug)) {
                $item->slug = Str::slug($item->name).'-'.Str::random(4);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function imageUrl(): ?string
    {
        return Category::resolveImageUrl($this->image);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
