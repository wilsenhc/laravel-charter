<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class StaticPageController extends Controller
{
    public function privacy(Request $request): InertiaResponse
    {
        return Inertia::render('Privacy');
    }

    public function terms(Request $request): InertiaResponse
    {
        return Inertia::render('Terms');
    }

    public function sitemap(): Response
    {
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

        $sitemap->add(
            Url::create(url('/privacy'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.3),
        );

        $sitemap->add(
            Url::create(url('/terms'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.3),
        );

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
    }
}
