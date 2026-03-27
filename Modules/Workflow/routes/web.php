<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::view('/workflow', 'workflow::index')->name('workflow.index');
});
