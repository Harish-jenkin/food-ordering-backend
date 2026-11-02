<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use App\Support\ApiResponse;

class SettingAdminController extends Controller
{
    public function __construct(private readonly SettingService $settings)
    {
    }

    public function show()
    {
        return ApiResponse::success($this->settings->all());
    }

    public function update(UpdateSettingRequest $request)
    {
        $updated = $this->settings->update($request->validated());

        return ApiResponse::success($updated, 'Settings saved.');
    }
}
