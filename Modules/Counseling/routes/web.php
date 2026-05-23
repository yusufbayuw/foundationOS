<?php

use Illuminate\Support\Facades\Route;
use Modules\Counseling\Http\Controllers\AnonymousReportController;

Route::prefix('parent')->group(function (): void {
    Route::post('anonymous-report', [AnonymousReportController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('parent.anonymous-report');
});
