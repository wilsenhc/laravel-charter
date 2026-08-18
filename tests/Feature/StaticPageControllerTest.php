<?php

use function Pest\Laravel\get;

it('renders the privacy page', function () {
    get(route('privacy', ['locale' => 'en']))
        ->assertSuccessful()
        ->assertSee('component":"Privacy"', false);
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
