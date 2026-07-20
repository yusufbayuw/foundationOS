<?php

use Illuminate\Support\Facades\Route;
use Modules\Library\Http\Controllers\FinePdfController;
use Modules\Library\Http\Controllers\LibraryController;
use Modules\Library\Http\Controllers\LoanPdfController;
use Modules\Library\Http\Controllers\PublicOpacController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('libraries', LibraryController::class)->names('library');
    Route::get('/library/loans/{loan}/pdf', LoanPdfController::class)
        ->name('library.loans.pdf');
    Route::get('/library/fines/{fine}/pdf', FinePdfController::class)
        ->name('library.fines.pdf');
});

Route::prefix('opac/{tenant}')->name('library.opac.')->group(function () {
    Route::get('/', [PublicOpacController::class, 'index'])->name('index');
    Route::get('/books/{book}', [PublicOpacController::class, 'show'])->name('show');
    Route::post('/books/{book}/reserve', [PublicOpacController::class, 'reserve'])
        ->middleware(['auth', 'verified'])
        ->name('reserve');
    Route::get('/circulation', [PublicOpacController::class, 'circulation'])
        ->middleware(['auth', 'verified'])
        ->name('circulation.index');
    Route::post('/circulation/checkout', [PublicOpacController::class, 'checkout'])
        ->middleware(['auth', 'verified', 'idempotency', 'throttle:checkout'])
        ->name('circulation.checkout');
    Route::post('/circulation/return', [PublicOpacController::class, 'quickReturn'])
        ->middleware(['auth', 'verified'])
        ->name('circulation.return');
    Route::post('/circulation/extend', [PublicOpacController::class, 'extendLoan'])
        ->middleware(['auth', 'verified'])
        ->name('circulation.extend');
    Route::post('/circulation/issue', [PublicOpacController::class, 'markIssue'])
        ->middleware(['auth', 'verified'])
        ->name('circulation.issue');
    Route::prefix('organizations/{organization}')->name('organization.')->group(function () {
        Route::get('/', [PublicOpacController::class, 'organizationIndex'])->name('index');
        Route::get('/books/{book}', [PublicOpacController::class, 'organizationShow'])->name('show');
        Route::post('/books/{book}/reserve', [PublicOpacController::class, 'organizationReserve'])
            ->middleware(['auth', 'verified'])
            ->name('reserve');
        Route::get('/circulation', [PublicOpacController::class, 'organizationCirculation'])
            ->middleware(['auth', 'verified'])
            ->name('circulation.index');
        Route::post('/circulation/checkout', [PublicOpacController::class, 'organizationCheckout'])
            ->middleware(['auth', 'verified', 'idempotency', 'throttle:checkout'])
            ->name('circulation.checkout');
        Route::post('/circulation/return', [PublicOpacController::class, 'organizationQuickReturn'])
            ->middleware(['auth', 'verified'])
            ->name('circulation.return');
        Route::post('/circulation/extend', [PublicOpacController::class, 'organizationExtendLoan'])
            ->middleware(['auth', 'verified'])
            ->name('circulation.extend');
        Route::post('/circulation/issue', [PublicOpacController::class, 'organizationMarkIssue'])
            ->middleware(['auth', 'verified'])
            ->name('circulation.issue');
    });
});
