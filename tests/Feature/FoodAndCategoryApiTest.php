<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\FoodItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodAndCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_active_categories_and_available_foods(): void
    {
        $category = Category::factory()->create();
        FoodItem::factory()->create(['category_id' => $category->id, 'is_available' => true]);
        FoodItem::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $this->getJson('/api/categories')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/foods')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_crud_categories_and_foods(): void
    {
        $admin = Admin::factory()->create();
        $token = $admin->createToken('admin-dashboard')->plainTextToken;

        $createCategory = $this->withToken($token)->postJson('/api/admin/categories', [
            'name' => 'Grill',
            'description' => 'From the tandoor',
        ]);
        $createCategory->assertCreated();
        $categoryId = $createCategory->json('data.id');

        $createFood = $this->withToken($token)->postJson('/api/admin/foods', [
            'category_id' => $categoryId,
            'name' => 'Tandoori Broccoli',
            'price' => 199.50,
            'is_veg' => true,
        ]);
        $createFood->assertCreated();
        $foodId = $createFood->json('data.id');

        $this->withToken($token)->putJson("/api/admin/foods/{$foodId}", [
            'price' => 210,
            'is_available' => false,
        ])->assertOk()->assertJsonPath('data.price', '210.00');

        $this->withToken($token)
            ->getJson('/api/admin/foods?is_veg=1&available=0')
            ->assertOk()
            ->assertJsonPath('data.meta.total', 1);

        $this->withToken($token)->deleteJson("/api/admin/foods/{$foodId}")->assertOk();
        $this->withToken($token)->deleteJson("/api/admin/categories/{$categoryId}")->assertOk();
    }
}
