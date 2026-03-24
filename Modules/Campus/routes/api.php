<?php

use Illuminate\Support\Facades\Route;
use Modules\Campus\Http\Controllers\CampusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('campuses', CampusController::class)->names('campus');
});
