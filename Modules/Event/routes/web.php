<?php

use Illuminate\Support\Facades\Route;
use Modules\Event\Http\Controllers\EventCertificatePdfController;

Route::middleware(['auth', 'verified'])->prefix('event')->group(function (): void {
    Route::get('/certificates/{eventCertificate}/pdf', EventCertificatePdfController::class)
        ->name('event.certificates.pdf');
});
