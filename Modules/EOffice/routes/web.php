<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\LetterPdfController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/eoffice/letters/{letter}/pdf', LetterPdfController::class)
        ->name('eoffice.letters.pdf');
});
