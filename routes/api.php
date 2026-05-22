<?php

use App\Http\Controllers\Api\v1\AuthController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

RateLimiter::for('api', function (Request $request) {
    $token = $request->user()?->currentAccessToken();
    $key = $token?->id ?? $request->ip();

    return Limit::perMinute(60)->by($key);
});

Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    Route::middleware(['auth:sanctum', 'resolve.api.tenant'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::get('tenants/current', [AuthController::class, 'currentTenant']);
    });
});
