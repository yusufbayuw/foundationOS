<?php

use Illuminate\Support\Facades\Route;
use Modules\Member\Http\Controllers\MemberRegistrationController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function (): void {
    Route::post('members/register', MemberRegistrationController::class)->name('members.register');
});
