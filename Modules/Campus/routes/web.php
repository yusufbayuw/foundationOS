<?php

use Illuminate\Support\Facades\Route;
use Modules\Campus\Http\Controllers\CollageStudentPdfController;
use Modules\Campus\Http\Controllers\StudyPlanPdfController;
use Modules\Campus\Http\Controllers\StudyResultPdfController;
use Modules\Campus\Http\Controllers\ThesisPdfController;
use Modules\Campus\Http\Controllers\WisudaPdfController;
use Modules\Campus\Http\Controllers\YudisiumPdfController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/campus/transcript/{collageStudent}/pdf', CollageStudentPdfController::class)
        ->name('campus.transcript.pdf');
    Route::get('/campus/study-plans/{studyPlan}/pdf', StudyPlanPdfController::class)
        ->name('campus.study-plans.pdf');
    Route::get('/campus/study-results/{studyResult}/pdf', StudyResultPdfController::class)
        ->name('campus.study-results.pdf');
    Route::get('/campus/theses/{thesis}/pdf', ThesisPdfController::class)
        ->name('campus.theses.pdf');
    Route::get('/campus/wisudas/{wisuda}/pdf', WisudaPdfController::class)
        ->name('campus.wisudas.pdf');
    Route::get('/campus/yudisiums/{yudisium}/pdf', YudisiumPdfController::class)
        ->name('campus.yudisiums.pdf');
});
