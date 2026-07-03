<?php

use function Pest\Laravel\get;

it('renders the privacy page', function () {
    get(route('privacy'))
        ->assertSuccessful()
        ->assertSee('Privacy Policy')
        ->assertSee('Last updated')
        ->assertSee('Information We Collect')
        ->assertSee('Cookies');
});

it('renders the terms page', function () {
    get(route('terms'))
        ->assertSuccessful()
        ->assertSee('Terms of Service')
        ->assertSee('Last updated')
        ->assertSee('Acceptance of Terms')
        ->assertSee('Open Source License');
});

it('generates a valid sitemap', function () {
    get(route('sitemap'))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/xml');
});

it('has links to privacy and terms on the home page', function () {
    get(route('build.index'))
        ->assertSuccessful()
        ->assertSee('Privacy')
        ->assertSee('Terms');
});
