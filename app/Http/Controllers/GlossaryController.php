<?php

namespace App\Http\Controllers;

use App\Models\GlossaryTerm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GlossaryController extends Controller
{
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $terms = GlossaryTerm::all()->map(fn (GlossaryTerm $term) => [
            'slug' => $term->slug,
            'category' => $term->category,
            'title' => $term->translations[$locale]['title'] ?? $term->translations['en']['title'] ?? $term->slug,
            'summary' => $term->translations[$locale]['summary'] ?? $term->translations['en']['summary'] ?? '',
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

        if ($glossaryTerm === null) {
            abort(404);
        }

        $translations = $glossaryTerm->translations[$locale]
            ?? $glossaryTerm->translations['en']
            ?? [];

        $relatedTerms = GlossaryTerm::whereIn('slug', $glossaryTerm->related)
            ->get()
            ->map(fn (GlossaryTerm $t) => [
                'slug' => $t->slug,
                'title' => $t->translations[$locale]['title'] ?? $t->translations['en']['title'] ?? $t->slug,
                'summary' => $t->translations[$locale]['summary'] ?? $t->translations['en']['summary'] ?? '',
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
