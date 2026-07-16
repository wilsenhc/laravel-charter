<?php

use App\Http\Controllers\BuildApplicationController;
use App\Http\Controllers\BuildPackageController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomepageController::class, 'index']);

Route::get('/application', [BuildApplicationController::class, 'index'])->name('build.application.index');

Route::get('/application/build', [BuildApplicationController::class, 'show'])->middleware('throttle:30,1')->name('build.application.show');

Route::get('/package', [BuildPackageController::class, 'index'])->name('build.package.index');

Route::get('/package/build', [BuildPackageController::class, 'show'])->middleware('throttle:30,1')->name('build.package.show');

Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');

Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');

Route::get('/sitemap.xml', [StaticPageController::class, 'sitemap'])->name('sitemap');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
