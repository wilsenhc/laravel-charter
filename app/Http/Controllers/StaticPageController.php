<?php

namespace App\Http\Controllers;

use App\Enums\Locale;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class StaticPageController extends Controller
{
    public function privacy(): InertiaResponse
    {
        return Inertia::render('Privacy');
    }

    public function terms(): InertiaResponse
    {
        return Inertia::render('Terms');
    }

    public function sitemap(): Response
    {
        $sitemap = Sitemap::create();

        $pages = [
            ['path' => 'application', 'priority' => 1.0, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['path' => 'package', 'priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['path' => 'stats', 'priority' => 0.5, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['path' => 'privacy', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['path' => 'terms', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($pages as $page) {
            $url = Url::create(url(Locale::default()->value.'/'.$page['path']))
                ->setLastModificationDate(now())
                ->setChangeFrequency($page['frequency'])
                ->setPriority($page['priority']);

            foreach (Locale::cases() as $locale) {
                $url->addAlternate($locale->value, url($locale->value.'/'.$page['path']));
            }

            $sitemap->add($url);
        }

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
    }
}
