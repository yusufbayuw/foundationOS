<?php

use Illuminate\Support\Facades\Route;
use Modules\Donation\Http\Controllers\DonationWebhookController;

Route::post('/donation/webhook', [DonationWebhookController::class, 'handle'])
    ->name('donation.webhook');
