<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Models\GlossaryTerm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ComparisonController extends Controller
{
    public function show(Request $request, string $comparison): Response
    {
        $locale = app()->getLocale();

        $slug = $request->route('comparison');

        $comparison = Comparison::where('slug', $slug)->first();

        if ($comparison === null) {
            abort(404);
        }

        $translations = $comparison->translations[$locale]
            ?? $comparison->translations['en']
            ?? [];

        $firstTerm = GlossaryTerm::where('slug', $comparison->first_term_slug)->first();
        $secondTerm = GlossaryTerm::where('slug', $comparison->second_term_slug)->first();

        $relatedComparisons = Comparison::whereIn('slug', $comparison->related)
            ->get()
            ->map(fn (Comparison $c) => [
                'slug' => $c->slug,
                'page_title' => $c->translations[$locale]['page_title'] ?? $c->translations['en']['page_title'] ?? $c->slug,
                'meta_description' => $c->translations[$locale]['meta_description'] ?? $c->translations['en']['meta_description'] ?? '',
            ])
            ->all();

        return Inertia::render('Comparison/Show', [
            'comparison' => $slug,
            'entry' => [
                'first' => $comparison->first_term_slug,
                'second' => $comparison->second_term_slug,
                'first_title' => $firstTerm?->translations[$locale]['title'] ?? $firstTerm?->translations['en']['title'] ?? $comparison->first_term_slug,
                'second_title' => $secondTerm?->translations[$locale]['title'] ?? $secondTerm?->translations['en']['title'] ?? $comparison->second_term_slug,
                'category' => $comparison->category,
                'translations' => $translations,
            ],
            'related' => $relatedComparisons,
        ]);
    }
}
