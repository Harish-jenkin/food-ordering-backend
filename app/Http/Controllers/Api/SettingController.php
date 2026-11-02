<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use App\Support\ApiResponse;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings)
    {
    }

    public function show()
    {
        return ApiResponse::success($this->settings->public());
    }
}
