<?php

use Illuminate\Support\Facades\Route;
use Modules\Donation\Http\Controllers\DonationCheckoutController;
use Modules\Donation\Http\Controllers\DonationPdfController;
use Modules\Donation\Http\Controllers\DonationWebhookController;

Route::post('/donation/checkout', [DonationCheckoutController::class, 'store'])
    ->name('donation.checkout');

Route::post('/donation/webhook', [DonationWebhookController::class, 'handle'])
    ->name('donation.webhook');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/donation/receipts/{donation}/pdf', DonationPdfController::class)
        ->name('donation.receipts.pdf');
});
