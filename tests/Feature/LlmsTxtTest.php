<?php

use App\Models\Comparison;
use App\Models\GlossaryTerm;

use function Pest\Laravel\get;

it('generates the llms.txt document', function () {
    GlossaryTerm::create([
        'slug' => 'laravel-sail',
        'category' => 'Concept',
        'builder_params' => [],
        'related' => [],
        'translations' => [
            'en' => [
                'title' => 'Laravel Sail',
                'question' => 'What is Laravel Sail?',
                'summary' => 'A lightweight CLI for managing the Laravel Docker environment.',
            ],
        ],
    ]);

    Comparison::create([
        'slug' => 'mysql-vs-mariadb',
        'first_term_slug' => 'mysql',
        'second_term_slug' => 'mariadb',
        'category' => 'Database',
        'related' => [],
        'translations' => [
            'en' => [
                'page_title' => 'MySQL vs MariaDB for Laravel',
                'meta_description' => 'Compare MySQL vs MariaDB for your Laravel Sail project.',
            ],
        ],
    ]);

    $content = get('/llms.txt')
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
        ->getContent();

    expect($content)
        ->toContain('# Charter for Laravel')
        ->toContain('## Build')
        ->toContain('## Glossary')
        ->toContain('## Comparisons')
        ->toContain('## For AI agents')
        ->toContain('## Optional')
        ->toContain('/en/application')
        ->toContain('/en/glossary/laravel-sail')
        ->toContain('/en/compare/mysql-vs-mariadb')
        ->toContain('/mcp/charter')
        ->toContain('/application/build?')
        ->toContain('/package/build?')
        ->toContain('services[] (mysql, mariadb, pgsql')
        ->toContain('features (comma-separated: config, routes')
        ->toContain('boolean flags: teams, no-node')
        ->toContain('Laravel Sail')
        ->toContain('MySQL vs MariaDB for Laravel');
});

it('serves glossary and comparison links from the database', function () {
    GlossaryTerm::create([
        'slug' => 'redis',
        'category' => 'Service',
        'builder_params' => [],
        'related' => [],
        'translations' => [
            'en' => [
                'title' => 'Redis',
                'question' => 'What is Redis?',
                'summary' => 'An in-memory data store for queues, cache, and sessions.',
            ],
        ],
    ]);

    $content = get('/llms.txt')->assertSuccessful()->getContent();

    expect($content)
        ->toContain('/en/glossary/redis')
        ->toContain('An in-memory data store for queues, cache, and sessions.');
});
