<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\AttendanceRecapPdfController;
use Modules\School\Http\Controllers\ClassGradeLedgerPdfController;
use Modules\School\Http\Controllers\ReportCardBulkPdfController;
use Modules\School\Http\Controllers\ReportCardController;
use Modules\School\Http\Controllers\ReportCardPdfController;
use Modules\School\Http\Controllers\StudentAchievementPdfController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('school/report-card/download', [ReportCardController::class, 'download'])
        ->name('school.report-card.download');

    Route::get('school/students/{student}/report-card/pdf', ReportCardPdfController::class)
        ->name('school.report-card.pdf');

    Route::get('school/classes/{schoolClass}/report-cards/bulk/pdf', ReportCardBulkPdfController::class)
        ->name('school.report-card.bulk.pdf');

    Route::get('school/classes/{schoolClass}/attendance-recap/pdf', AttendanceRecapPdfController::class)
        ->name('school.attendance-recap.pdf');

    Route::get('school/student-achievements/{studentAchievement}/pdf', StudentAchievementPdfController::class)
        ->name('school.student-achievements.pdf');

    Route::get('school/classes/{schoolClass}/grade-ledger/pdf', ClassGradeLedgerPdfController::class)
        ->name('school.grade-ledger.pdf');
});
