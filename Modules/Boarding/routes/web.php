<?php

use Illuminate\Support\Facades\Route;
use Modules\Boarding\Http\Controllers\BoardingLeavePermitPdfController;

Route::middleware(['auth', 'verified'])->prefix('boarding')->group(function (): void {
    Route::get('/leave-permits/{boardingLeavePermit}/pdf', BoardingLeavePermitPdfController::class)
        ->name('boarding.leave-permits.pdf');
});
