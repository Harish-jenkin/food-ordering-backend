<?php

namespace Tests\Feature;

use App\Models\FoodItem;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::query()->insert([
            ['key' => 'whatsapp_number', 'value' => '919966738715', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_charge', 'value' => '40', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'free_delivery_min', 'value' => '499', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_percent', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'discount_percent', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_order_uses_backend_prices_not_client_prices(): void
    {
        $food = FoodItem::factory()->create(['price' => 100, 'is_available' => true]);

        $response = $this->postJson('/api/orders', [
            'items' => [
                ['food_item_id' => $food->id, 'quantity' => 2, 'price' => 1],
            ],
            'customer_name' => 'Asha',
            'customer_mobile' => '9966738715',
            'order_type' => 'takeaway',
            'payment_method' => 'cash',
            'idempotency_key' => '11111111-1111-1111-1111-111111111111',
        ]);

        $response->assertCreated();
        $this->assertSame('200.00', $response->json('data.order.subtotal'));
        $this->assertSame('210.00', $response->json('data.order.grand_total'));
        $this->assertSame('919966738715', $response->json('data.whatsapp_number'));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_items', [
            'name' => $food->name,
            'unit_price' => 100,
            'quantity' => 2,
        ]);
    }

    public function test_unavailable_food_cannot_be_ordered(): void
    {
        $food = FoodItem::factory()->create(['is_available' => false]);

        $this->postJson('/api/orders', [
            'items' => [['food_item_id' => $food->id, 'quantity' => 1]],
            'customer_name' => 'Asha',
            'customer_mobile' => '9966738715',
            'order_type' => 'takeaway',
            'payment_method' => 'upi',
        ])->assertStatus(422);
    }

    public function test_delivery_requires_address(): void
    {
        $food = FoodItem::factory()->create();

        $this->postJson('/api/orders', [
            'items' => [['food_item_id' => $food->id, 'quantity' => 1]],
            'customer_name' => 'Asha',
            'customer_mobile' => '9966738715',
            'order_type' => 'delivery',
            'payment_method' => 'card',
        ])->assertStatus(422);
    }

    public function test_idempotency_key_prevents_duplicate_orders(): void
    {
        $food = FoodItem::factory()->create(['price' => 50]);
        $payload = [
            'items' => [['food_item_id' => $food->id, 'quantity' => 1]],
            'customer_name' => 'Asha',
            'customer_mobile' => '9966738715',
            'order_type' => 'takeaway',
            'payment_method' => 'cash',
            'idempotency_key' => '22222222-2222-2222-2222-222222222222',
        ];

        $this->postJson('/api/orders', $payload)->assertCreated();
        $this->postJson('/api/orders', $payload)->assertCreated();
        $this->assertSame(1, Order::query()->count());
    }

    public function test_order_lookup_requires_matching_mobile(): void
    {
        $food = FoodItem::factory()->create();
        $create = $this->postJson('/api/orders', [
            'items' => [['food_item_id' => $food->id, 'quantity' => 1]],
            'customer_name' => 'Asha',
            'customer_mobile' => '9966738715',
            'order_type' => 'takeaway',
            'payment_method' => 'cash',
        ]);

        $number = $create->json('data.order.order_number');

        $this->getJson("/api/orders/{$number}?mobile=0000000000")->assertNotFound();
        $this->getJson("/api/orders/{$number}?mobile=9966738715")->assertOk();
    }
}
