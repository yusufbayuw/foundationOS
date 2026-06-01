<?php

use Illuminate\Support\Facades\Route;
use Modules\Enrollment\Http\Controllers\ApplicantAcceptancePdfController;
use Modules\Enrollment\Http\Controllers\ApplicantRejectionPdfController;
use Modules\Enrollment\Http\Controllers\EnrollmentController;
use Modules\Enrollment\Http\Controllers\ExamSchedulePdfController;
use Modules\Enrollment\Http\Controllers\InquiryController;
use Modules\Enrollment\Http\Controllers\RegistrationPdfController;

Route::middleware(['throttle:10,1'])->post('/inquiry', [InquiryController::class, 'store'])
    ->name('enrollment.inquiry');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('enrollments', EnrollmentController::class)->names('enrollment');

    Route::get('/enrollment/applicants/{applicant}/acceptance/pdf', ApplicantAcceptancePdfController::class)
        ->name('enrollment.applicants.acceptance.pdf');
    Route::get('/enrollment/applicants/{applicant}/rejection/pdf', ApplicantRejectionPdfController::class)
        ->name('enrollment.applicants.rejection.pdf');
    Route::get('/enrollment/registrations/{registration}/pdf', RegistrationPdfController::class)
        ->name('enrollment.registrations.pdf');
    Route::get('/enrollment/exam-schedules/{examSchedule}/pdf', ExamSchedulePdfController::class)
        ->name('enrollment.exam-schedules.pdf');
});
