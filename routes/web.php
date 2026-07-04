<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BuildController::class, 'index'])->name('build.index');

Route::get('/build', [BuildController::class, 'show'])->middleware('throttle:30,1')->name('build.show');

Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');

Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');

Route::get('/sitemap.xml', [StaticPageController::class, 'sitemap'])->name('sitemap');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
