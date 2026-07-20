<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\CustomerInvoicePdfController;
use Modules\Finance\Http\Controllers\FinancialReportController;
use Modules\Finance\Http\Controllers\PaymentPdfController;
use Modules\Finance\Http\Controllers\StudentInvoicePdfController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/finance/student-invoices/{studentInvoice}/pdf', StudentInvoicePdfController::class)
        ->name('finance.student-invoices.pdf');
    Route::get('/finance/payments/{payment}/pdf', PaymentPdfController::class)
        ->name('finance.payments.pdf');
    Route::get('/finance/customer-invoices/{customerInvoice}/pdf', CustomerInvoicePdfController::class)
        ->name('finance.customer-invoices.pdf');

    Route::prefix('finance/reports')->name('finance.reports.')->group(function (): void {
        Route::get('/profit-loss/pdf', [FinancialReportController::class, 'profitLossPdf'])
            ->name('profit-loss.pdf');
        Route::get('/balance-sheet/pdf', [FinancialReportController::class, 'balanceSheetPdf'])
            ->name('balance-sheet.pdf');
        Route::get('/cash-flow/pdf', [FinancialReportController::class, 'cashFlowPdf'])
            ->name('cash-flow.pdf');
    });
});
