<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\SalesOrderPdfController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sales/orders/{salesOrder}/pdf', SalesOrderPdfController::class)
        ->name('sales.orders.pdf');
});
