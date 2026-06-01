<?php

use Illuminate\Support\Facades\Route;
use Modules\Legal\Http\Controllers\ContractPdfController;

Route::middleware(['auth', 'verified'])->prefix('legal')->group(function (): void {
    Route::get('/contracts/{contract}/pdf', ContractPdfController::class)
        ->name('legal.contracts.pdf');
});
