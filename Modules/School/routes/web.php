<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\SchoolController;
use Modules\School\Http\Controllers\ReportCardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('schools', SchoolController::class)->names('school');
    
    Route::get('school/report-card/download', [ReportCardController::class, 'download'])
        ->name('school.report-card.download');
});
