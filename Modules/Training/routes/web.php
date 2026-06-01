<?php

use Illuminate\Support\Facades\Route;
use Modules\Training\Http\Controllers\CertificateVerificationController;
use Modules\Training\Http\Controllers\TrainingCertificatePdfController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/training/certificates/{trainingCertificate}/pdf', TrainingCertificatePdfController::class)
        ->whereNumber('trainingCertificate')
        ->name('training.certificates.pdf');
});

Route::get('/training/certificates/{token}', [CertificateVerificationController::class, 'show'])
    ->name('training.certificate.verify');
