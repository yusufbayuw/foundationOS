<?php

use Illuminate\Support\Facades\Route;
use Modules\MerchOrder\Http\Controllers\MerchOrderPdfController;

Route::middleware(['auth', 'verified'])->prefix('merch')->group(function (): void {
    Route::get('/orders/{merchOrder}/pdf', MerchOrderPdfController::class)
        ->name('merch.orders.pdf');
});
