<?php

use App\Jobs\RecordApplicationBuildStat;
use App\Models\ApplicationStat;

test('stores stat record with services', function () {
    $job = new RecordApplicationBuildStat(
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
    );

    $job->handle();

    $this->assertDatabaseHas('application_stats', [
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

    $stat = ApplicationStat::first();
    expect($stat->services->pluck('name')->toArray())
        ->toEqualCanonicalizing(['redis', 'pgsql']);
});

test('stores stat record with none services', function () {
    $job = new RecordApplicationBuildStat(
        data: ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'],
        services: ['none'],
    );

    $job->handle();

    $this->assertDatabaseCount('application_stats', 1);

    $stat = ApplicationStat::first();
    expect($stat->services)->toBeEmpty();
});

test('does not create duplicate stat within dedup window', function () {
    $data = ['php_version' => '8.5', 'starter_kit' => 'none', 'testing_framework' => 'pest'];

    $job1 = new RecordApplicationBuildStat(
        data: $data,
        services: ['none'],
    );

    $job1->handle();

    $job2 = new RecordApplicationBuildStat(
        data: $data,
        services: ['none'],
    );

    $job2->handle();

    $this->assertDatabaseCount('application_stats', 1);
});
