<?php

use Illuminate\Support\Facades\Route;
use Modules\Consulting\Http\Controllers\EngagementInvoicePdfController;

Route::middleware(['auth', 'verified'])->prefix('consulting')->group(function (): void {
    Route::get('/engagement-invoices/{engagementInvoice}/pdf', EngagementInvoicePdfController::class)
        ->name('consulting.engagement-invoices.pdf');
});
