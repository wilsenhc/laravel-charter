<?php

use App\Http\Controllers\BuildController;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Route::get('/', [BuildController::class, 'index'])->name('build.index');

Route::get('/build', [BuildController::class, 'show'])->name('build.show');

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create();

    $sitemap->add(
        Url::create(url('/'))
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(1.0),
    );

    $sitemap->add(
        Url::create(url('/build'))
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9),
    );

    return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');
