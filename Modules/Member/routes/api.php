<?php

use Illuminate\Support\Facades\Route;
use Modules\Member\Http\Controllers\MemberRegistrationController;

Route::middleware(['auth:sanctum', 'resolve.api.tenant', 'throttle:api'])->prefix('v1')->group(function (): void {
    Route::post('members/register', MemberRegistrationController::class)
        ->middleware('abilities:api:write')
        ->name('members.register');
});
