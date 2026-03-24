<?php

use Illuminate\Support\Facades\Route;
use Modules\Global\Http\Controllers\GlobalController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('globals', GlobalController::class)->names('global');
});
