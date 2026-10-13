<?php

namespace App\Http\Controllers;

use App\Actions\BuildGuideCtaAction;
use App\Enums\Locale;
use App\Models\Guide;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Head\Facades\Head;
use Laravel\Head\Facades\Schema;

class GuideController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $locale = app()->getLocale();

        Head::title(__('guides.page_title'))
            ->description(__('guides.meta_description'))
            ->canonical('/'.$locale.'/guides');

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('guides.page_title') => url($locale.'/guides'),
        ]));

        $guides = Guide::all()
            ->filter(fn (Guide $guide): bool => $guide->hasFullTranslation($locale)
                && $guide->hasFullTranslation(Locale::default()->value))
            ->map(fn (Guide $guide): array => [
                'slug' => $guide->slug,
                'category' => $guide->category,
                'title' => $guide->translations[$locale]['title'],
                'description' => $guide->translations[$locale]['meta_description'],
            ])->values()->all();

        return Inertia::render('Guides/Index', [
            'guides' => $guides,
        ]);
    }

    public function show(Request $request, BuildGuideCtaAction $buildCta, string $guide): InertiaResponse
    {
        $locale = app()->getLocale();

        $slug = $request->route('guide');

        $guide = Guide::where('slug', $slug)->first();

        if ($guide === null || ! $guide->hasFullTranslation($locale)) {
            abort(404);
        }

        $translations = $guide->translations[$locale];

        Head::title($translations['title'])
            ->description($translations['meta_description'])
            ->canonical('/'.$locale.'/guides/'.$slug);

        $alternateLocales = collect(Locale::cases())
            ->filter(fn (Locale $candidate): bool => $guide->hasFullTranslation($candidate->value));

        Head::alternates($alternateLocales
            ->mapWithKeys(fn (Locale $candidate): array => [
                $candidate->value => url($candidate->value.'/guides/'.$slug),
            ])->all());

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('guides.page_title') => url($locale.'/guides'),
            $translations['heading'] => url($locale.'/guides/'.$slug),
        ]));

        $related = Guide::whereIn('slug', $guide->related)
            ->get()
            ->filter(fn (Guide $relatedGuide): bool => $relatedGuide->hasFullTranslation($locale))
            ->map(fn (Guide $relatedGuide): array => [
                'slug' => $relatedGuide->slug,
                'title' => $relatedGuide->translations[$locale]['title'],
                'description' => $relatedGuide->translations[$locale]['meta_description'],
            ])->all();

        return Inertia::render('Guides/Show', [
            'guide' => $slug,
            'entry' => [
                'category' => $guide->category,
                'translations' => $translations,
            ],
            'related' => $related,
            'cta' => $buildCta($guide, $locale),
        ]);
    }
}
