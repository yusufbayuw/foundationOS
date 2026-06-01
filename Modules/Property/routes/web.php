<?php

use Illuminate\Support\Facades\Route;
use Modules\Property\Http\Controllers\LeaseInvoicePdfController;

Route::middleware(['auth', 'verified'])->prefix('property')->group(function (): void {
    Route::get('/lease-invoices/{leaseInvoice}/pdf', LeaseInvoicePdfController::class)
        ->name('property.lease-invoices.pdf');
});
