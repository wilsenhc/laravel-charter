<?php

use App\Models\Guide;

use function Pest\Laravel\get;

it('renders the privacy page', function () {
    get(route('privacy', ['locale' => 'en']))
        ->assertSuccessful()
        ->assertSee('component":"Privacy"', false);
});

it('shares the site origin with the frontend for structured data', function () {
    $content = get(route('privacy', ['locale' => 'en']))
        ->assertSuccessful()
        ->getContent();

    expect($content)->toMatch('/"origin":"https?:\\\\\/\\\\\/[^"]+"/');
});

it('renders the terms page', function () {
    get(route('terms', ['locale' => 'en']))
        ->assertSuccessful()
        ->assertSee('component":"Terms"', false);
});

it('generates a valid sitemap', function () {
    get(route('sitemap'))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertSee('hreflang="en"', false)
        ->assertSee('hreflang="es"', false);
});

it('includes the guides and the mcp page in the sitemap', function () {
    Guide::create([
        'slug' => 'sitemap-guide',
        'category' => 'Application',
        'app_name' => 'my-app',
        'builder_params' => ['services' => ['pgsql']],
        'related' => [],
        'translations' => [
            'en' => [
                'title' => 'Sitemap guide',
                'meta_description' => 'Sitemap guide description.',
                'heading' => 'Sitemap guide heading',
                'problem' => 'Sitemap guide problem.',
                'notes' => ['Note.'],
                'steps' => ['Step.'],
            ],
            'es' => [
                'title' => 'Guía del sitemap',
                'meta_description' => 'Descripción de la guía.',
                'heading' => 'Título de la guía',
                'problem' => 'Problema de la guía.',
                'notes' => ['Nota.'],
                'steps' => ['Paso.'],
            ],
        ],
    ]);

    $content = get(route('sitemap'))->getContent();

    expect($content)
        ->toContain('/en/guides')
        ->toContain('/en/guides/sitemap-guide')
        ->toContain('/es/guides/sitemap-guide')
        ->toContain('/en/mcp')
        ->toContain('/es/mcp');
});

it('excludes locales without a full translation from guide sitemap entries', function () {
    Guide::create([
        'slug' => 'partial-sitemap-guide',
        'category' => 'Application',
        'app_name' => 'my-app',
        'builder_params' => [],
        'related' => [],
        'translations' => [
            'en' => [
                'title' => 'Partial guide',
                'meta_description' => 'Partial guide description.',
                'heading' => 'Partial guide heading',
                'problem' => 'Partial guide problem.',
                'notes' => ['Note.'],
                'steps' => ['Step.'],
            ],
            'es' => [
                'title' => '',
                'meta_description' => '',
                'heading' => '',
                'problem' => '',
                'notes' => [],
                'steps' => [],
            ],
        ],
    ]);

    $content = get(route('sitemap'))->getContent();

    expect($content)
        ->toContain('/en/guides/partial-sitemap-guide')
        ->not->toContain('/es/guides/partial-sitemap-guide');
});

it('renders the application page', function () {
    get(route('build.application.index', ['locale' => 'en']))
        ->assertSuccessful()
        ->assertSee('component":"Build', false);
});

it('emits PWA meta tags and manifest link', function () {
    $content = get(route('build.application.index', ['locale' => 'en']))
        ->assertSuccessful()
        ->getContent();

    expect($content)->toMatch('/<link rel="manifest" href="[^"]+manifest\.webmanifest"[^>]*>/')
        ->and($content)->toMatch('/<meta name="theme-color" content="#171717">/')
        ->and($content)->toMatch('/<link rel="apple-touch-icon" href="[^"]+apple-touch-icon-180x180\.png"[^>]*>/');
});

it('ships the PWA offline fallback page', function () {
    $content = file_get_contents(public_path('offline.html'));

    expect($content)->not->toBeFalse()
        ->and($content)->toContain('offline')
        ->and($content)->toContain('retry');
});
