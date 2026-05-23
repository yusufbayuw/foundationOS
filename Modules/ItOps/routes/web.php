<?php

use Illuminate\Support\Facades\Route;
use Modules\ItOps\Http\Controllers\MonitoringWebhookController;

Route::prefix('itops')->group(function (): void {
    Route::post('/monitoring/webhook', MonitoringWebhookController::class)
        ->name('itops.monitoring.webhook');
});
