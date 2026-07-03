<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BuildController::class, 'index'])->name('build.index');

Route::get('/build', [BuildController::class, 'show'])->name('build.show');

Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');

Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');

Route::get('/sitemap.xml', [StaticPageController::class, 'sitemap'])->name('sitemap');
