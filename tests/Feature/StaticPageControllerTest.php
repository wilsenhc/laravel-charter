<?php

use function Pest\Laravel\get;

it('renders the privacy page', function () {
    get(route('privacy'))
        ->assertSuccessful()
        ->assertSee('component":"Privacy"', false);
});

it('renders the terms page', function () {
    get(route('terms'))
        ->assertSuccessful()
        ->assertSee('component":"Terms"', false);
});

it('generates a valid sitemap', function () {
    get(route('sitemap'))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/xml');
});

it('renders the home page', function () {
    get(route('build.index'))
        ->assertSuccessful()
        ->assertSee('component":"Build', false);
});
