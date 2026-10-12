<?php

use App\Models\Guide;
use Illuminate\Http\Response;

use function Pest\Laravel\get;

function createGuide(array $overrides = []): Guide
{
    return Guide::create(array_merge([
        'slug' => 'laravel-sail-with-postgresql',
        'category' => 'Application',
        'app_name' => 'my-pg-app',
        'builder_params' => [
            'services' => ['pgsql'],
            'database' => 'pgsql',
            'frontend' => 'none',
            'testing' => 'pest',
            'javascript' => 'bun',
            'php' => '8.5',
        ],
        'related' => ['laravel-sail-with-mysql'],
        'translations' => [
            'en' => [
                'title' => 'Laravel Sail with PostgreSQL — Setup Guide',
                'meta_description' => 'Run Laravel Sail with PostgreSQL.',
                'heading' => 'Run Laravel Sail with PostgreSQL',
                'problem' => 'PostgreSQL for advanced SQL.',
                'notes' => ['Notes paragraph one.'],
                'steps' => ['Step one.'],
            ],
            'es' => [
                'title' => 'Laravel Sail con PostgreSQL — Guía de configuración',
                'meta_description' => 'Ejecuta Laravel Sail con PostgreSQL.',
                'heading' => 'Ejecuta Laravel Sail con PostgreSQL',
                'problem' => 'PostgreSQL para SQL avanzado.',
                'notes' => ['Nota uno.'],
                'steps' => ['Paso uno.'],
            ],
        ],
    ], $overrides));
}

it('renders the guides index', function () {
    createGuide();

    get(route('guides.index', ['locale' => 'en']))
        ->assertSuccessful()
        ->assertSee('component":"Guides\/Index"', false);
});

it('renders a guide with metadata, canonical, hreflang, and breadcrumbs', function () {
    createGuide();

    $content = get(route('guides.show', ['locale' => 'en', 'guide' => 'laravel-sail-with-postgresql']))
        ->assertSuccessful()
        ->getContent();

    expect($content)
        ->toContain('component":"Guides\/Show"')
        ->toContain('<title data-inertia="title">Laravel Sail with PostgreSQL — Setup Guide — Charter for Laravel</title>')
        ->toContain('<meta data-inertia="description" name="description" content="Run Laravel Sail with PostgreSQL.">')
        ->toContain('<link data-inertia="canonical" rel="canonical" href="https://laravel-charter.test/en/guides/laravel-sail-with-postgresql">')
        ->toContain('<link data-inertia="alternate:en" rel="alternate" hreflang="en" href="https://laravel-charter.test/en/guides/laravel-sail-with-postgresql">')
        ->toContain('<link data-inertia="alternate:es" rel="alternate" hreflang="es" href="https://laravel-charter.test/es/guides/laravel-sail-with-postgresql">')
        ->toContain('"BreadcrumbList"');
});

it('preselects the builder with the curated options', function () {
    createGuide();

    get(route('guides.show', ['locale' => 'en', 'guide' => 'laravel-sail-with-postgresql']))
        ->assertSuccessful()
        ->assertSee('"cta":{"type":"application","href":"\/en\/application?name=my-pg-app', false);
});

it('builds a package builder link for package guides', function () {
    createGuide([
        'slug' => 'laravel-package-with-commands-and-migrations',
        'category' => 'Package',
        'app_name' => 'my-package',
        'builder_params' => [
            'features' => ['config', 'routes', 'migrations', 'commands'],
            'php' => '8.5',
        ],
    ]);

    get(route('guides.show', ['locale' => 'en', 'guide' => 'laravel-package-with-commands-and-migrations']))
        ->assertSuccessful()
        ->assertSee('"cta":{"type":"package","href":"\/en\/package?name=my-package', false);
});

it('drops unsupported builder params from the CTA link', function () {
    createGuide([
        'slug' => 'tampered-guide',
        'builder_params' => [
            'services' => ['oracle'],
            'database' => 'bogus',
        ],
    ]);

    $content = get(route('guides.show', ['locale' => 'en', 'guide' => 'tampered-guide']))
        ->assertSuccessful()
        ->getContent();

    expect($content)
        ->toContain('"href":"\/en\/application?name=my-pg-app"')
        ->not->toContain('oracle')
        ->not->toContain('bogus');
});

it('returns 404 for unknown guides', function () {
    createGuide();

    get(route('guides.show', ['locale' => 'en', 'guide' => 'does-not-exist']))
        ->assertStatus(Response::HTTP_NOT_FOUND);
});

it('returns 404 for locales without a full translation', function () {
    createGuide([
        'slug' => 'partial-guide',
        'translations' => [
            'en' => [
                'title' => 'English guide',
                'meta_description' => 'English description.',
                'heading' => 'English heading',
                'problem' => 'English problem.',
                'notes' => ['English note.'],
                'steps' => ['English step.'],
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

    get(route('guides.show', ['locale' => 'es', 'guide' => 'partial-guide']))
        ->assertStatus(Response::HTTP_NOT_FOUND);
});

it('localizes the guide content', function () {
    createGuide();

    get(route('guides.show', ['locale' => 'es', 'guide' => 'laravel-sail-with-postgresql']))
        ->assertSuccessful()
        ->assertSee('component":"Guides\/Show"', false)
        ->assertSee('Ejecuta Laravel Sail con PostgreSQL', false)
        ->assertSee('"\/es\/application?name=my-pg-app', false);
});

it('lists the guide in llms.txt', function () {
    createGuide();

    get('/llms.txt')
        ->assertSuccessful()
        ->getContent();

    $content = get('/llms.txt')->getContent();

    expect($content)
        ->toContain('## Guides')
        ->toContain('/en/guides/laravel-sail-with-postgresql');
});
