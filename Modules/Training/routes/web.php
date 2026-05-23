<?php

use Illuminate\Support\Facades\Route;
use Modules\Training\Http\Controllers\CertificateVerificationController;

Route::get('/training/certificates/{token}', [CertificateVerificationController::class, 'show'])
    ->name('training.certificate.verify');
