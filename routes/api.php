<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CategoryAdminController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\FoodAdminController;
use App\Http\Controllers\Api\Admin\OrderAdminController;
use App\Http\Controllers\Api\Admin\SettingAdminController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/foods', [FoodItemController::class, 'index']);
Route::get('/foods/{slug}', [FoodItemController::class, 'show']);
Route::get('/settings', [SettingController::class, 'show']);
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:orders');

Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::apiResource('categories', CategoryAdminController::class);
        Route::post('/categories/{category}', [CategoryAdminController::class, 'update']);
        Route::apiResource('foods', FoodAdminController::class);
        Route::post('/foods/{food}', [FoodAdminController::class, 'update']);
        Route::get('/orders', [OrderAdminController::class, 'index']);
        Route::get('/orders/{order}', [OrderAdminController::class, 'show']);
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus']);
        Route::get('/settings', [SettingAdminController::class, 'show']);
        Route::put('/settings', [SettingAdminController::class, 'update']);
    });
});
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'Laravel API is running successfully',
    ]);
});