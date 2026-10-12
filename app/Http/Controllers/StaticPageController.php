<?php

namespace App\Http\Controllers;

use App\Enums\Locale;
use App\Models\Comparison;
use App\Models\GlossaryTerm;
use App\Models\Guide;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Head\Facades\Head;
use Laravel\Head\Facades\Schema;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class StaticPageController extends Controller
{
    public function privacy(): InertiaResponse
    {
        $locale = app()->getLocale();

        Head::title(__('privacy.title'))
            ->description(__('privacy.meta_description'))
            ->canonical('/'.$locale.'/privacy');

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('privacy.title') => url($locale.'/privacy'),
        ]));

        return Inertia::render('Privacy');
    }

    public function terms(): InertiaResponse
    {
        $locale = app()->getLocale();

        Head::title(__('terms.title'))
            ->description(__('terms.meta_description'))
            ->canonical('/'.$locale.'/terms');

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('terms.title') => url($locale.'/terms'),
        ]));

        return Inertia::render('Terms');
    }

    public function sitemap(): Response
    {
        $sitemap = Sitemap::create();

        /** @var array<int, array{path: string, priority: float, frequency: string, locales: array<string>, lastmod: CarbonImmutable}> $pages */
        $pages = [
            ['path' => 'application', 'priority' => 1.0, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'package', 'priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'stats', 'priority' => 0.5, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'privacy', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'terms', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'glossary', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'mcp', 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY, 'locales' => Locale::codes(), 'lastmod' => now()],
            ['path' => 'guides', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'locales' => Locale::codes(), 'lastmod' => now()],
        ];

        foreach (Guide::all() as $guide) {
            $pages[] = [
                'path' => "guides/{$guide->slug}",
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_MONTHLY,
                'locales' => $guide->publishedLocales(),
                'lastmod' => $guide->updated_at ?? now(),
            ];
        }

        foreach (GlossaryTerm::all() as $term) {
            $pages[] = [
                'path' => "glossary/{$term->slug}",
                'priority' => 0.6,
                'frequency' => Url::CHANGE_FREQUENCY_MONTHLY,
                'locales' => $term->publishedLocales(),
                'lastmod' => $term->updated_at ?? now(),
            ];
        }

        foreach (Comparison::all() as $comparison) {
            $pages[] = [
                'path' => "compare/{$comparison->slug}",
                'priority' => 0.7,
                'frequency' => Url::CHANGE_FREQUENCY_MONTHLY,
                'locales' => $comparison->publishedLocales(),
                'lastmod' => $comparison->updated_at ?? now(),
            ];
        }

        foreach ($pages as $page) {
            $url = Url::create(url(Locale::default()->value.'/'.$page['path']))
                ->setLastModificationDate($page['lastmod'])
                ->setChangeFrequency($page['frequency'])
                ->setPriority($page['priority']);

            foreach ($page['locales'] as $locale) {
                $url->addAlternate(url($locale.'/'.$page['path']), $locale);
            }

            $sitemap->add($url);
        }

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
