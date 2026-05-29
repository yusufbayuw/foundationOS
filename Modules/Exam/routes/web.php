<?php

use Illuminate\Support\Facades\Route;
use Modules\Exam\Http\Controllers\ExamParticipantTokenPdfController;
use Modules\Exam\Http\Controllers\ExamResultsPdfController;

Route::middleware(['web', 'auth'])->prefix('admin')->group(function (): void {
    Route::get('/exams/{exam}/participant-tokens.pdf', ExamParticipantTokenPdfController::class)
        ->name('exam.participant-tokens.pdf');

    Route::get('/exams/{exam}/results.pdf', ExamResultsPdfController::class)
        ->name('exam.results.pdf');
});
