<?php

use Illuminate\Support\Facades\Route;
use Modules\Campus\Http\Controllers\CampusController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('campuses', CampusController::class)->names('campus');
});
