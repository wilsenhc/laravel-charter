<?php

use App\Models\Service;
use App\Models\Stat;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

describe('index', function () {
    test('renders the stats page with empty state', function () {
        get(route('stats.index'))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stats/Index')
                    ->where('total', 0)
                    ->has('phpVersions')
                    ->has('services')
                    ->has('starterKits')
                    ->has('javascriptRuntimes')
                    ->has('authProviders')
                    ->has('testingFrameworks')
                    ->has('booleanOptions')
                    ->has('filters'),
            );
    });

    test('shows aggregated stats from stored builds', function () {
        Stat::factory()->count(3)->create([
            'php_version' => '8.5',
            'starter_kit' => 'react',
        ]);

        Stat::factory()->count(2)->create([
            'php_version' => '8.4',
            'starter_kit' => 'vue',
        ]);

        get(route('stats.index'))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stats/Index')
                    ->where('total', 5)
                    ->where('phpVersions', ['8.5' => 3, '8.4' => 2])
                    ->where('starterKits', ['react' => 3, 'vue' => 2]),
            );
    });

    test('filters by date range', function () {
        Stat::factory()->create([
            'created_at' => now()->subDays(10),
        ]);

        Stat::factory()->create([
            'created_at' => now()->subDays(5),
        ]);

        Stat::factory()->create([
            'created_at' => now()->subDays(1),
        ]);

        $from = now()->subDays(7)->format('Y-m-d');

        get(route('stats.index', ['from' => $from]))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stats/Index')
                    ->where('total', 2)
                    ->where('filters.from', $from),
            );
    });

    test('filters by to date', function () {
        Stat::factory()->create([
            'created_at' => now()->subDays(10),
        ]);

        Stat::factory()->create([
            'created_at' => now()->subDays(5),
        ]);

        $to = now()->subDays(7)->format('Y-m-d');

        get(route('stats.index', ['to' => $to]))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stats/Index')
                    ->where('total', 1),
            );
    });

    test('filters by both from and to', function () {
        Stat::factory()->create([
            'created_at' => now()->subDays(20),
        ]);

        Stat::factory()->create([
            'created_at' => now()->subDays(12),
        ]);

        Stat::factory()->create([
            'created_at' => now()->subDays(8),
        ]);

        Stat::factory()->create([
            'created_at' => now()->subDays(2),
        ]);

        $from = now()->subDays(15)->format('Y-m-d');
        $to = now()->subDays(6)->format('Y-m-d');

        get(route('stats.index', ['from' => $from, 'to' => $to]))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stats/Index')
                    ->where('total', 2),
            );
    });

    test('aggregates services correctly', function () {
        $redis = Service::where('name', 'redis')->first();
        $pgsql = Service::where('name', 'pgsql')->first();

        $stat1 = Stat::factory()->create();
        $stat1->services()->sync([$redis->id, $pgsql->id]);

        $stat2 = Stat::factory()->create();
        $stat2->services()->sync([$redis->id]);

        $stat3 = Stat::factory()->create();
        $stat3->services()->sync([$pgsql->id, $redis->id]);

        get(route('stats.index'))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stats/Index')
                    ->where('total', 3)
                    ->has('services', function (Assert $services) {
                        $services->where('redis', 3)
                            ->where('pgsql', 2);
                    }),
            );
    });
});
