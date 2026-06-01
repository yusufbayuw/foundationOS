<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\EmployeeController;
use Modules\Employee\Http\Controllers\EmploymentContractPdfController;
use Modules\Employee\Http\Controllers\LeaveRequestPdfController;
use Modules\Employee\Http\Controllers\SalarySlipController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('employees', EmployeeController::class)->names('employee');
    Route::get('/salary-slips/{salarySlip}/download', [SalarySlipController::class, 'download'])
        ->name('employee.salary-slips.download');
    Route::get('/employee/leave-requests/{leaveRequest}/pdf', LeaveRequestPdfController::class)
        ->name('employee.leave-requests.pdf');
    Route::get('/employee/employment-contracts/{employmentContract}/pdf', EmploymentContractPdfController::class)
        ->name('employee.employment-contracts.pdf');
});
