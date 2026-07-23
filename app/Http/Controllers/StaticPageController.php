<?php

namespace App\Http\Controllers;

use App\Enums\Locale;
use App\Models\Comparison;
use App\Models\GlossaryTerm;
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
            ['path' => 'glossary', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
        ];

        foreach (GlossaryTerm::all() as $term) {
            $pages[] = ['path' => "glossary/{$term->slug}", 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY];
        }

        foreach (Comparison::all() as $comparison) {
            $pages[] = ['path' => "compare/{$comparison->slug}", 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY];
        }

        foreach ($pages as $page) {
            $url = Url::create(url(Locale::default()->value.'/'.$page['path']))
                ->setLastModificationDate(now())
                ->setChangeFrequency($page['frequency'])
                ->setPriority($page['priority']);

            foreach (Locale::cases() as $locale) {
                $url->addAlternate(url($locale->value.'/'.$page['path']), $locale->value);
            }

            $sitemap->add($url);
        }

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
    }
}
