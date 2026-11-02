<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_protected_routes(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@spicehaven.test',
            'password' => 'Admin@12345',
        ]);

        $login = $this->postJson('/api/admin/login', [
            'email' => 'admin@spicehaven.test',
            'password' => 'Admin@12345',
        ]);

        $login->assertOk()->assertJsonPath('success', true);
        $token = $login->json('data.token');

        $this->withToken($token)
            ->getJson('/api/admin/me')
            ->assertOk()
            ->assertJsonPath('data.email', $admin->email);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        Admin::factory()->create(['email' => 'admin@spicehaven.test']);

        $this->postJson('/api/admin/login', [
            'email' => 'admin@spicehaven.test',
            'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->getJson('/api/admin/dashboard')->assertUnauthorized();
    }
}
