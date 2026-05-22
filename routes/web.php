<?php

use App\Http\Controllers\LocaleSwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/locale/{locale}', [LocaleSwitchController::class, 'switch'])
        ->where('locale', 'id|en')
        ->name('locale.switch');
});
