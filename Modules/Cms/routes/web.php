<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\PublicSiteController;

Route::prefix('cms')->group(function (): void {
    Route::get('{site}/pages/{slug}', [PublicSiteController::class, 'page'])->name('cms.page');
    Route::get('{site}/sitemap.xml', [PublicSiteController::class, 'sitemap'])->name('cms.sitemap');
    Route::post('{site}/contact', [PublicSiteController::class, 'contact'])
        ->middleware('throttle:10,1')
        ->name('cms.contact');
});
