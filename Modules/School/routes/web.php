<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\SchoolController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('schools', SchoolController::class)->names('school');
});
