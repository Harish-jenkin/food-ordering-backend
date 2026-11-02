<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public const CACHE_KEY = 'restaurant_settings';

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 120, function () {
            return Setting::query()->pluck('value', 'key')->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function decimal(string $key, string $default = '0'): string
    {
        return number_format((float) $this->get($key, $default), 2, '.', '');
    }

    public function public(): array
    {
        $settings = $this->all();

        return [
            'restaurant_name' => $settings['restaurant_name'] ?? 'Spice Haven',
            'tagline' => $settings['tagline'] ?? '',
            'about' => $settings['about'] ?? '',
            'phone' => $settings['phone'] ?? '',
            'email' => $settings['email'] ?? '',
            'address' => $settings['address'] ?? '',
            'opening_hours' => $settings['opening_hours'] ?? '',
            'whatsapp_number' => $settings['whatsapp_number'] ?? '',
            'upi_id' => $settings['upi_id'] ?? '',
            'upi_name' => $settings['upi_name'] ?? ($settings['restaurant_name'] ?? 'Spice Haven'),
            'delivery_charge' => $this->decimal('delivery_charge'),
            'free_delivery_min' => $this->decimal('free_delivery_min'),
            'tax_percent' => $this->decimal('tax_percent'),
            'discount_percent' => $this->decimal('discount_percent'),
            'currency' => $settings['currency'] ?? 'INR',
            'hero_image' => $settings['hero_image'] ?? null,
        ];
    }

    public function update(array $values): array
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }

        Cache::forget(self::CACHE_KEY);

        return $this->all();
    }
}
