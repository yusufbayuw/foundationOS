<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\FinanceController;
use Modules\Finance\Http\Controllers\FinancialReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('finances', FinanceController::class)->names('finance');

    Route::prefix('finance/reports')->name('finance.reports.')->group(function (): void {
        Route::get('/profit-loss/pdf', [FinancialReportController::class, 'profitLossPdf'])
            ->name('profit-loss.pdf');
        Route::get('/balance-sheet/pdf', [FinancialReportController::class, 'balanceSheetPdf'])
            ->name('balance-sheet.pdf');
        Route::get('/cash-flow/pdf', [FinancialReportController::class, 'cashFlowPdf'])
            ->name('cash-flow.pdf');
    });
});
