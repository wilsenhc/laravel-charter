<?php

use App\Jobs\RecordPackageBuildStat;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

describe('index', function () {
    test('renders the build package page', function () {
        $this->get(route('build.package.index', ['locale' => 'en']))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Build/Package')
                    ->has('url')
                    ->where('url', fn (string $url) => str_starts_with($url, 'http')),
            );
    });
});

describe('show', function () {
    test('returns the build script for a valid request', function () {
        $response = $this->get('/package/build?name=my-package');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('laravel package my-package');
    });

    test('does not accept invalid features', function () {
        $response = $this->get('/package/build?name=my-package&features=invalid-feature');

        $response->assertStatus(400);
        $response->assertSee('Invalid feature', false);
    });

    test('does not accept invalid package_name format', function () {
        $response = $this->get('/package/build?name=my-package&package_name=invalid');

        $response->assertStatus(400);
        $response->assertSee('Package name must be in the format vendor/package', false);
    });

    test('does not accept invalid package name', function () {
        $response = $this->get('/package/build?name=invalid.name');

        $response->assertStatus(400);
        $response->assertSee('Invalid package name', false);
    });

    test('package name is required', function () {
        $response = $this->get('/package/build');

        $response->assertStatus(400);
        $response->assertSee('Invalid package name', false);
    });

    test('package name is sanitized in the error output', function () {
        $response = $this->get('/package/build?name=evil$(whoami)evil');

        $response->assertStatus(400);
        $response->assertDontSee('$(whoami)', false);
    });

    test('does not accept invalid php version', function () {
        $response = $this->get('/package/build?name=my-package&php=7.4');

        $response->assertStatus(400);
        $response->assertSee('Invalid PHP version', false);
    });

    test('stores anonymous stats on build request', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/package/build?name=my-package&features=config,routes&php=8.5');

        Queue::assertPushed(RecordPackageBuildStat::class, function (RecordPackageBuildStat $job) {
            return $job->data['php_version'] === '8.5'
                && $job->data['config'] === true
                && $job->data['routes'] === true
                && $job->data['views'] === false
                && $job->data['boost_skill'] === false
                    && $job->data['mcp_source'] === 'web';
        });
    });

    test('stores no features when none are selected', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/package/build?name=my-package&php=8.4');

        Queue::assertPushed(RecordPackageBuildStat::class, function (RecordPackageBuildStat $job) {
            return $job->data['php_version'] === '8.4'
                && $job->data['config'] === false
                && $job->data['routes'] === false
                && $job->data['boost_skill'] === false
                    && $job->data['mcp_source'] === 'web';
        });
    });

    describe('user agent filtering', function () {
        test('does not dispatch job for browser requests', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/134.0.0.0 Safari/537.36')
                ->get('/package/build?name=my-package');

            Queue::assertNotPushed(RecordPackageBuildStat::class);
        });

        test('does not dispatch job for browser-based crawler requests', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
                ->get('/package/build?name=my-package');

            Queue::assertNotPushed(RecordPackageBuildStat::class);
        });

        test('dispatches job for curl requests', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'curl/8.5.0')
                ->get('/package/build?name=my-package');

            Queue::assertPushed(RecordPackageBuildStat::class);
        });

        test('dispatches job for empty user agent header', function () {
            Queue::fake();

            $this->withHeader('User-Agent', '')
                ->get('/package/build?name=my-package');

            Queue::assertPushed(RecordPackageBuildStat::class);
        });
    });
});
