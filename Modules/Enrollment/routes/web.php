<?php

use Illuminate\Support\Facades\Route;
use Modules\Enrollment\Http\Controllers\EnrollmentController;
use Modules\Enrollment\Http\Controllers\InquiryController;

Route::middleware(['throttle:10,1'])->post('/inquiry', [InquiryController::class, 'store'])
    ->name('enrollment.inquiry');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('enrollments', EnrollmentController::class)->names('enrollment');
});
