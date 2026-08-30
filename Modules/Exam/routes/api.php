<?php

use Illuminate\Support\Facades\Route;
use Modules\Exam\Http\Controllers\Api\ExamRuntimeWebhookController;

Route::prefix('exam')->middleware([
    'auth:sanctum',
    'throttle:api',
    'resolve.api.tenant',
    'abilities:exam:attempts:write',
    'permission:sync_exam_result',
])->group(function (): void {
    Route::post('runtime/attempts', [ExamRuntimeWebhookController::class, 'storeAttempt'])
        ->name('exam.runtime.attempts.store');
});
