<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\SalesController;
use Modules\Sales\Http\Controllers\SalesOrderPdfController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sales', SalesController::class)->names('sales');
    Route::get('/sales/orders/{salesOrder}/pdf', SalesOrderPdfController::class)
        ->name('sales.orders.pdf');
});
