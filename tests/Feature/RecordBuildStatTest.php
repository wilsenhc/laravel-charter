<?php

use App\Jobs\RecordBuildStat;
use App\Models\Stat;

test('stores stat record for curl user agent', function () {
    $job = new RecordBuildStat(
        data: [
            'php_version' => '8.5',
            'starter_kit' => 'react',
            'custom_starter_kit' => false,
            'javascript_runtime' => 'bun',
            'auth_provider' => 'laravel',
            'testing_framework' => 'pest',
            'teams' => false,
            'boost' => true,
            'devcontainer' => false,
            'no_node' => false,
            'livewire_class_components' => false,
        ],
        services: ['redis', 'pgsql'],
        userAgent: 'curl/8.5',
    );

    $job->handle();

    $this->assertDatabaseHas('stats', [
        'php_version' => '8.5',
        'starter_kit' => 'react',
        'custom_starter_kit' => false,
        'javascript_runtime' => 'bun',
        'auth_provider' => 'laravel',
        'testing_framework' => 'pest',
        'teams' => false,
        'boost' => true,
        'devcontainer' => false,
        'no_node' => false,
        'livewire_class_components' => false,
    ]);

    $stat = Stat::first();
    expect($stat->services->pluck('name')->toArray())
        ->toEqualCanonicalizing(['redis', 'pgsql']);
});

test('stores stat record for wget user agent', function () {
    $job = new RecordBuildStat(
        data: ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'],
        services: ['none'],
        userAgent: 'Wget/1.21',
    );

    $job->handle();

    $this->assertDatabaseCount('stats', 1);
});

test('stores stat record for non-browser user agent', function () {
    $job = new RecordBuildStat(
        data: ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'],
        services: ['none'],
        userAgent: 'HTTPie/3.2',
    );

    $job->handle();

    $this->assertDatabaseCount('stats', 1);
});

test('does not store stat for browser user agent', function () {
    $job = new RecordBuildStat(
        data: ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'],
        services: ['none'],
        userAgent: 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/134.0.0.0 Safari/537.36',
    );

    $job->handle();

    $this->assertDatabaseCount('stats', 0);
});

test('does not store stat for browser-based crawler user agent', function () {
    $job = new RecordBuildStat(
        data: ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'],
        services: ['none'],
        userAgent: 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    );

    $job->handle();

    $this->assertDatabaseCount('stats', 0);
});

test('does not create duplicate stat within dedup window', function () {
    $data = ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'];

    $job1 = new RecordBuildStat(
        data: $data,
        services: ['none'],
        userAgent: 'curl/8.5',
    );

    $job1->handle();

    $job2 = new RecordBuildStat(
        data: $data,
        services: ['none'],
        userAgent: 'curl/8.5',
    );

    $job2->handle();

    $this->assertDatabaseCount('stats', 1);
});
