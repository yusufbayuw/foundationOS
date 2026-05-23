<?php

use Illuminate\Support\Facades\Route;
use Modules\Enrollment\Http\Controllers\InquiryController;

Route::middleware(['throttle:10,1'])->group(function (): void {
    Route::post('/inquiry', [InquiryController::class, 'store'])->name('enrollment.inquiry');
});
