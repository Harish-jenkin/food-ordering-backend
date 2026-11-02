<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\FoodItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FoodItemFactory extends Factory
{
    protected $model = FoodItem::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 49, 499),
            'is_veg' => true,
            'is_available' => true,
            'is_popular' => false,
            'is_bestseller' => false,
            'is_special' => false,
        ];
    }
}
