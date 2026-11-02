<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\FoodItem;
use App\Models\Order;
use App\Support\ApiResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $recent = Order::query()->with('items')->latest()->limit(8)->get();

        return ApiResponse::success([
            'stats' => [
                'orders_today' => Order::query()->where('created_at', '>=', $today)->count(),
                'revenue_today' => number_format((float) Order::query()->where('created_at', '>=', $today)->where('status', '!=', 'cancelled')->sum('grand_total'), 2, '.', ''),
                'pending_orders' => Order::query()->where('status', 'pending')->count(),
                'total_orders' => Order::query()->count(),
                'food_items' => FoodItem::query()->count(),
                'unavailable_items' => FoodItem::query()->where('is_available', false)->count(),
            ],
            'recent_orders' => OrderResource::collection($recent)->resolve(),
        ]);
    }
}
