<?php

namespace Database\Seeders;

use App\Services\SettingService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        app(SettingService::class)->update([
            'restaurant_name' => 'Spice Haven',
            'tagline' => 'Wood-fired Indian kitchen. Fresh, fast, unforgettable.',
            'about' => 'Spice Haven is a neighbourhood Indian kitchen in the heart of the city. We cook with whole spices, slow gravies, and tandoor heat — then pack every order so it arrives the way it left the pass. Dine in, take away, or let us bring dinner to your door.',
            'phone' => '+91 99667 38715',
            'email' => 'harishmamindlapelli2@gmail.com',
            'address' => '14, MG Road, Bengaluru 560001',
            'opening_hours' => 'Daily 11:00 AM – 11:00 PM',
            'whatsapp_number' => '919966738715',
            'upi_id' => 'spicehaven@upi',
            'upi_name' => 'Spice Haven',
            'delivery_charge' => '40.00',
            'free_delivery_min' => '499.00',
            'tax_percent' => '5.00',
            'discount_percent' => '0.00',
            'currency' => 'INR',
            'hero_image' => 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=1600&q=80',
        ]);
    }
}
