<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FoodItem;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::query()->insert([
            ['key' => 'whatsapp_number', 'value' => '919966738715', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_charge', 'value' => '40', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'free_delivery_min', 'value' => '499', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_percent', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'discount_percent', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_admin_can_filter_and_update_orders(): void
    {
        $food = FoodItem::factory()->create(['price' => 100]);
        $create = $this->postJson('/api/orders', [
            'items' => [['food_item_id' => $food->id, 'quantity' => 1]],
            'customer_name' => 'Asha',
            'customer_mobile' => '9966738715',
            'order_type' => 'takeaway',
            'payment_method' => 'cash',
        ]);
        $orderId = $create->json('data.order.id');

        $admin = Admin::factory()->create();
        $token = $admin->createToken('admin-dashboard')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/admin/orders?status=pending&search=Asha&order_type=takeaway')
            ->assertOk()
            ->assertJsonPath('data.meta.total', 1);

        $this->withToken($token)
            ->getJson('/api/admin/orders?order_type=delivery')
            ->assertOk()
            ->assertJsonPath('data.meta.total', 0);

        $this->withToken($token)
            ->patchJson("/api/admin/orders/{$orderId}/status", ['status' => 'confirmed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed');
    }

    public function test_admin_can_update_whatsapp_settings(): void
    {
        $admin = Admin::factory()->create();
        $token = $admin->createToken('admin-dashboard')->plainTextToken;

        $this->withToken($token)
            ->putJson('/api/admin/settings', [
                'whatsapp_number' => '911112223334',
                'delivery_charge' => 50,
                'upi_id' => 'kitchen@okaxis',
                'upi_name' => 'Spice Haven Kitchen',
            ])
            ->assertOk()
            ->assertJsonPath('data.whatsapp_number', '911112223334')
            ->assertJsonPath('data.upi_id', 'kitchen@okaxis');

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('data.upi_id', 'kitchen@okaxis')
            ->assertJsonPath('data.upi_name', 'Spice Haven Kitchen');
    }
}
