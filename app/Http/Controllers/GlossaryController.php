<?php

namespace App\Http\Controllers;

use App\Models\GlossaryTerm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Head\Facades\Head;
use Laravel\Head\Facades\Schema;

class GlossaryController extends Controller
{
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        Head::title(__('glossary.page_title'))
            ->description(__('glossary.meta_description'))
            ->canonical('/'.$locale.'/glossary');

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('glossary.page_title') => url($locale.'/glossary'),
        ]));

        $terms = GlossaryTerm::all()
            ->filter(fn (GlossaryTerm $term): bool => $term->hasFullTranslation($locale))
            ->map(fn (GlossaryTerm $term) => [
                'slug' => $term->slug,
                'category' => $term->category,
                'title' => $term->translations[$locale]['title'],
                'summary' => $term->translations[$locale]['summary'],
            ])->values()->all();

        return Inertia::render('Glossary/Index', [
            'terms' => $terms,
        ]);
    }

    public function show(Request $request, string $term): Response
    {
        $locale = app()->getLocale();

        $slug = $request->route('term');

        $glossaryTerm = GlossaryTerm::where('slug', $slug)->first();

        if ($glossaryTerm === null || ! $glossaryTerm->hasFullTranslation($locale)) {
            abort(404);
        }

        $translations = $glossaryTerm->translations[$locale];

        $page_title = $translations['question'] ?? $translations['title'] ?? $slug;
        $summary = $translations['summary'] ?? '';

        Head::title($page_title)
            ->description($summary)
            ->canonical('/'.$locale.'/glossary/'.$slug);

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('glossary.page_title') => url($locale.'/glossary'),
            $page_title => url($locale.'/glossary/'.$slug),
        ]));

        $relatedTerms = GlossaryTerm::whereIn('slug', $glossaryTerm->related)
            ->get()
            ->filter(fn (GlossaryTerm $t): bool => $t->hasFullTranslation($locale))
            ->map(fn (GlossaryTerm $t) => [
                'slug' => $t->slug,
                'title' => $t->translations[$locale]['title'],
                'summary' => $t->translations[$locale]['summary'],
            ])
            ->all();

        return Inertia::render('Glossary/Show', [
            'term' => $slug,
            'entry' => [
                'category' => $glossaryTerm->category,
                'builder_params' => $glossaryTerm->builder_params,
                'translations' => $translations,
            ],
            'related' => $relatedTerms,
        ]);
    }
}
