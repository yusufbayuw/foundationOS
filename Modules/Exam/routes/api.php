<?php

use Illuminate\Support\Facades\Route;
use Modules\Exam\Http\Controllers\Api\ExamRuntimeWebhookController;

Route::prefix('exam')->middleware(['auth:sanctum'])->group(function (): void {
    Route::post('runtime/attempts', [ExamRuntimeWebhookController::class, 'storeAttempt'])
        ->name('exam.runtime.attempts.store');
});
