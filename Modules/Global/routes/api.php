<?php

use Illuminate\Support\Facades\Route;
use Modules\Global\Http\Controllers\GlobalController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('globals', GlobalController::class)->names('global');
});
