<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SettingService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly SettingService $settings,
    ) {
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orders->create($request->validated());

        return ApiResponse::success([
            'order' => (new OrderResource($order))->resolve(),
            'whatsapp_number' => $this->settings->get('whatsapp_number'),
        ], 'Order saved. Please send the WhatsApp message to confirm with the restaurant.', 201);
    }

    public function show(Request $request, string $orderNumber)
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $mobile = preg_replace('/\s+/', '', (string) $request->query('mobile', ''));

        if ($mobile === '' || $mobile !== $order->customer_mobile) {
            return ApiResponse::error('Order not found for the provided mobile number.', 404);
        }

        return ApiResponse::success(new OrderResource($order));
    }
}
