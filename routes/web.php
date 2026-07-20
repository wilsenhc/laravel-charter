<?php

use App\Enums\Locale;
use App\Http\Controllers\BuildApplicationController;
use App\Http\Controllers\BuildPackageController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')
    ->whereIn('locale', Locale::codes())
    ->group(function () {
        Route::get('/application', [BuildApplicationController::class, 'index'])->name('build.application.index');
        Route::get('/package', [BuildPackageController::class, 'index'])->name('build.package.index');
        Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
        Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');
        Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');
        Route::get('/glossary', [GlossaryController::class, 'index'])->name('glossary.index');
        Route::get('/glossary/{term}', [GlossaryController::class, 'show'])->name('glossary.show');
        Route::get('/compare/{comparison}', [ComparisonController::class, 'show'])->name('comparison.show');
    });

Route::get('/', [HomepageController::class, 'index']);
Route::get('/application', [BuildApplicationController::class, 'index']);
Route::get('/application/build', [BuildApplicationController::class, 'show'])->name('build.application.show');
Route::get('/package', [BuildPackageController::class, 'index']);
Route::get('/package/build', [BuildPackageController::class, 'show'])->name('build.package.show');
Route::get('/stats', [StatsController::class, 'index']);
Route::get('/privacy', [StaticPageController::class, 'privacy']);
Route::get('/terms', [StaticPageController::class, 'terms']);
Route::get('/glossary', [GlossaryController::class, 'index']);
Route::get('/glossary/{term}', [GlossaryController::class, 'show']);
Route::get('/compare/{comparison}', [ComparisonController::class, 'show']);

Route::get('/sitemap.xml', [StaticPageController::class, 'sitemap'])->name('sitemap');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
