<?php

use App\Jobs\RecordApplicationBuildStat;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

describe('index', function () {
    test('renders the build application page', function () {
        $this->get(route('build.application.index', ['locale' => 'en']))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Build/Application')
                    ->has('url')
                    ->where('url', fn (string $url) => str_starts_with($url, 'http')),
            );
    });
});

describe('show', function () {
    test('returns the build script for a valid request', function () {
        $response = $this->get('/application/build?name=my-app&services=pgsql,redis');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('laravel new my-app');
    });

    test('allows none as the only service', function () {
        $response = $this->get('/application/build?name=my-app&services=none');

        $response->assertSuccessful();
        $response->assertSee('--with=none');
    });

    test('does not accept none with other services', function () {
        $response = $this->get('/application/build?name=my-app&services=none,redis');

        $response->assertStatus(400);
        $response->assertSee('Invalid service name', false);
    });

    test('does not accept invalid services', function () {
        $response = $this->get('/application/build?name=my-app&services=invalid-service');

        $response->assertStatus(400);
        $response->assertSee('Invalid service name', false);
    });

    test('does not accept invalid javascript runtimes', function () {
        $response = $this->get('/application/build?name=my-app&javascript=invalid-runtime');

        $response->assertStatus(400);
        $response->assertSee('Invalid JavaScript runtime', false);
    });

    test('does not accept invalid auth provider', function () {
        $response = $this->get('/application/build?name=my-app&auth=invalid-auth');

        $response->assertStatus(400);
        $response->assertSee('Invalid authentication provider', false);
    });

    test('does not accept invalid php version', function () {
        $response = $this->get('/application/build?name=my-app&php=7.4');

        $response->assertStatus(400);
        $response->assertSee('Invalid PHP version', false);
    });

    test('does not accept invalid database driver', function () {
        $response = $this->get('/application/build?name=my-app&database=invalid-db');

        $response->assertStatus(400);
        $response->assertSee('Invalid database driver', false);
    });

    test('custom starter kit url is required when using custom frontend', function () {
        $response = $this->get('/application/build?name=my-app&frontend=custom');

        $response->assertStatus(400);
        $response->assertSee('A valid URL is required when using a custom starter kit', false);
    });

    test('does not accept invalid custom starter kit url', function () {
        $response = $this->get('/application/build?name=my-app&frontend=custom&using=not-a-url');

        $response->assertStatus(400);
        $response->assertSee('Invalid custom starter kit URL', false);
    });

    test('does not accept invalid application name', function () {
        $response = $this->get('/application/build?name=invalid.name');

        $response->assertStatus(400);
        $response->assertSee('Invalid application name', false);
    });

    test('application name is required', function () {
        $response = $this->get('/application/build?services=redis');

        $response->assertStatus(400);
        $response->assertSee('Invalid application name', false);
    });

    test('application name is sanitized in the error output', function () {
        $response = $this->get('/application/build?name=evil$(whoami)evil');

        $response->assertStatus(400);
        $response->assertDontSee('$(whoami)', false);
    });

    test('stores anonymous stats on build request', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/application/build?name=my-app&services=redis,pgsql&frontend=react&javascript=bun&auth=laravel&testing=pest&php=8.5&boost&database=pgsql');

        Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
            return $job->data['php_version'] === '8.5'
                && $job->data['starter_kit'] === 'react'
                && $job->data['custom_starter_kit'] === false
                && $job->data['javascript_runtime'] === 'bun'
                && $job->data['auth_provider'] === 'laravel'
                && $job->data['testing_framework'] === 'pest'
                && $job->data['teams'] === false
                && $job->data['boost'] === true
                && $job->data['devcontainer'] === false
                && $job->data['no_node'] === false
                && $job->data['livewire_class_components'] === false
                && $job->data['database_driver'] === 'pgsql'
                && $job->data['mcp_source'] === 'web';
        });
    });

    test('stores services in pivot table', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/application/build?name=my-app&services=redis,pgsql');

        Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
            return $job->services === ['redis', 'pgsql'];
        });
    });

    test('does not store name or custom url or ip', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/application/build?name=my-secret-app&services=redis&frontend=custom&using=https://example.com/kit');

        Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
            return $job->data['custom_starter_kit'] === true
                && $job->data['starter_kit'] === 'custom'
                && $job->data['mcp_source'] === 'web';
        });
    });

    test('stores none services without pivot entries', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/application/build?name=my-app&services=none');

        Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
            return $job->services === ['none'];
        });
    });

    describe('user agent filtering', function () {
        test('does not dispatch job for browser requests', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/134.0.0.0 Safari/537.36')
                ->get('/application/build?name=my-app');

            Queue::assertNotPushed(RecordApplicationBuildStat::class);
        });

        test('does not dispatch job for browser-based crawler requests', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
                ->get('/application/build?name=my-app');

            Queue::assertNotPushed(RecordApplicationBuildStat::class);
        });

        test('dispatches job for curl requests', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'curl/8.5.0')
                ->get('/application/build?name=my-app');

            Queue::assertPushed(RecordApplicationBuildStat::class);
        });

        test('dispatches job for empty user agent header', function () {
            Queue::fake();

            $this->withHeader('User-Agent', '')
                ->get('/application/build?name=my-app');

            Queue::assertPushed(RecordApplicationBuildStat::class);
        });
    });
});
