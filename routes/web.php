<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\LocaleSwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Midtrans webhook — excluded from CSRF via TrustHosts / bootstrap/app.php
Route::post('/billing/webhook', [BillingController::class, 'webhook'])->name('billing.webhook');
Route::get('/billing/finish/{tenant}', [BillingController::class, 'finish'])->name('billing.finish');

Route::middleware('auth')->group(function () {
    Route::get('/locale/{locale}', [LocaleSwitchController::class, 'switch'])
        ->where('locale', 'id|en')
        ->name('locale.switch');
});
