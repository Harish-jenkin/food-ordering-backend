<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Starters', 'slug' => 'starters', 'description' => 'Crisp, smoky plates to open the meal.', 'sort_order' => 1],
            ['name' => 'Mains', 'slug' => 'mains', 'description' => 'Signature gravies and tandoor classics.', 'sort_order' => 2],
            ['name' => 'Biryani', 'slug' => 'biryani', 'description' => 'Layered rice, slow-cooked spices.', 'sort_order' => 3],
            ['name' => 'Breads', 'slug' => 'breads', 'description' => 'Fresh from the tandoor.', 'sort_order' => 4],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'A sweet finish.', 'sort_order' => 5],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Lassi, chai, and coolers.', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['is_active' => true]
            );
        }
    }
}
