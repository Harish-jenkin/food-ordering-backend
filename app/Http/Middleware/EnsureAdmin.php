<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Admin) {
            return ApiResponse::error('Unauthorized.', 401);
        }

        return $next($request);
    }
}
