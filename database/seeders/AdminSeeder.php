<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->updateOrCreate(
            ['email' => 'admin@spicehaven.test'],
            [
                'name' => 'Restaurant Admin',
                'password' => 'Admin@12345',
            ]
        );
    }
}
