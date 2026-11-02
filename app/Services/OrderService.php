<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\FoodItem;
use App\Models\Order;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private readonly SettingService $settings)
    {
    }

    /**
     * Recalculate an order from trusted product records. Client prices are ignored.
     */
    public function quote(array $payload): array
    {
        $quantities = collect($payload['items'])
            ->groupBy('food_item_id')
            ->map(fn ($group) => (int) $group->sum('quantity'));

        $foods = FoodItem::query()
            ->whereIn('id', $quantities->keys())
            ->get()
            ->keyBy('id');

        if ($foods->count() !== $quantities->count()) {
            throw ValidationException::withMessages([
                'items' => ['One or more food items could not be found.'],
            ]);
        }

        $lines = [];
        $subtotal = 0.0;

        foreach ($quantities as $foodId => $quantity) {
            /** @var FoodItem $food */
            $food = $foods->get($foodId);

            if (! $food->is_available) {
                throw ValidationException::withMessages([
                    'items' => ["{$food->name} is currently unavailable."],
                ]);
            }

            $unitPrice = (float) $food->price;
            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;

            $lines[] = [
                'food' => $food,
                'quantity' => $quantity,
                'unit_price' => Money::round($unitPrice),
                'line_total' => Money::round($lineTotal),
            ];
        }

        $discountPercent = (float) $this->settings->get('discount_percent', 0);
        $taxPercent = (float) $this->settings->get('tax_percent', 0);
        $deliveryCharge = 0.0;

        if (($payload['order_type'] ?? 'delivery') === 'delivery') {
            $deliveryCharge = (float) $this->settings->get('delivery_charge', 0);
            $freeMin = (float) $this->settings->get('free_delivery_min', 0);
            if ($freeMin > 0 && $subtotal >= $freeMin) {
                $deliveryCharge = 0.0;
            }
        }

        $discount = round($subtotal * ($discountPercent / 100), 2);
        $taxable = max($subtotal - $discount, 0);
        $tax = round($taxable * ($taxPercent / 100), 2);
        $grandTotal = $taxable + $deliveryCharge + $tax;

        return [
            'lines' => $lines,
            'subtotal' => Money::round($subtotal),
            'delivery_charge' => Money::round($deliveryCharge),
            'discount' => Money::round($discount),
            'tax' => Money::round($tax),
            'grand_total' => Money::round($grandTotal),
        ];
    }

    public function create(array $payload): Order
    {
        return DB::transaction(function () use ($payload) {
            if (! empty($payload['idempotency_key'])) {
                $existing = Order::query()
                    ->with('items')
                    ->where('idempotency_key', $payload['idempotency_key'])
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            $quote = $this->quote($payload);

            $customer = Customer::query()->firstOrNew([
                'mobile' => preg_replace('/\s+/', '', $payload['customer_mobile']),
            ]);
            $customer->name = $payload['customer_name'];
            $customer->email = $payload['customer_email'] ?? $customer->email;
            $customer->save();

            $order = Order::query()->create([
                'order_number' => $this->uniqueOrderNumber(),
                'idempotency_key' => $payload['idempotency_key'] ?? null,
                'customer_id' => $customer->id,
                'customer_name' => $payload['customer_name'],
                'customer_mobile' => $customer->mobile,
                'customer_email' => $payload['customer_email'] ?? null,
                'order_type' => $payload['order_type'],
                'status' => 'pending',
                'payment_method' => $payload['payment_method'],
                'delivery_address' => $payload['order_type'] === 'delivery' ? $payload['delivery_address'] : null,
                'landmark' => $payload['landmark'] ?? null,
                'preferred_time' => $payload['preferred_time'] ?? null,
                'instructions' => $payload['instructions'] ?? null,
                'subtotal' => $quote['subtotal'],
                'delivery_charge' => $quote['delivery_charge'],
                'discount' => $quote['discount'],
                'tax' => $quote['tax'],
                'grand_total' => $quote['grand_total'],
            ]);

            foreach ($quote['lines'] as $line) {
                $order->items()->create([
                    'food_item_id' => $line['food']->id,
                    'name' => $line['food']->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);
            }

            return $order->load('items');
        });
    }

    private function uniqueOrderNumber(): string
    {
        do {
            $number = 'SH-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
