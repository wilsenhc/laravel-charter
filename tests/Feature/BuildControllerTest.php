<?php

use App\Jobs\RecordBuildStat;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

describe('index', function () {
    test('renders the build index page', function () {
        $this->get(route('build.index'))
            ->assertSuccessful()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Build/Index')
                    ->has('url')
                    ->where('url', fn (string $url) => str_starts_with($url, 'http')),
            );
    });
});

describe('show', function () {
    test('returns the build script for a valid request', function () {
        $response = $this->get('/build?name=my-app&services=pgsql,redis');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('laravel new my-app');
        $response->assertSee('sail:install --with=pgsql,redis --php=8.5');
    });

    test('allows none as the only service', function () {
        $response = $this->get('/build?name=my-app&services=none');

        $response->assertSuccessful();
        $response->assertSee('--with=none');
    });

    test('does not accept none with other services', function () {
        $response = $this->get('/build?name=my-app&services=none,redis');

        $response->assertStatus(400);
        $response->assertSee('Invalid service name', false);
    });

    test('does not accept invalid services', function () {
        $response = $this->get('/build?name=my-app&services=invalid-service');

        $response->assertStatus(400);
        $response->assertSee('Invalid service name', false);
    });

    test('livewire starter kit can be picked', function () {
        $response = $this->get('/build?name=my-app&frontend=livewire');

        $response->assertSuccessful();
        $response->assertSee('--livewire');
    });

    test('livewire class components modifier can be added', function () {
        $response = $this->get('/build?name=my-app&frontend=livewire&livewire-class-components');

        $response->assertSuccessful();
        $response->assertSee('--livewire --livewire-class-components');
    });

    test('livewire without class components does not add the modifier', function () {
        $response = $this->get('/build?name=my-app&frontend=livewire');

        $response->assertSuccessful();
        $response->assertSee('--livewire');
        $response->assertDontSee('--livewire-class-components');
    });

    test('react starter kit can be picked', function () {
        $response = $this->get('/build?name=my-app&frontend=react');

        $response->assertSuccessful();
        $response->assertSee('--react');
    });

    test('vue starter kit can be picked', function () {
        $response = $this->get('/build?name=my-app&frontend=vue');

        $response->assertSuccessful();
        $response->assertSee('--vue');
    });

    test('svelte starter kit can be picked', function () {
        $response = $this->get('/build?name=my-app&frontend=svelte');

        $response->assertSuccessful();
        $response->assertSee('--svelte');
    });

    test('custom starter kit does not add a frontend flag', function () {
        $response = $this->get('/build?name=my-app&frontend=custom&using=https://example.com/starter-kit');

        $response->assertSuccessful();
        $response->assertDontSee('--custom');
        $response->assertSee('--using="https://example.com/starter-kit"', false);
    });

    test('custom starter kit installs node binaries for npx', function () {
        $response = $this->get('/build?name=my-app&frontend=custom&using=https://example.com/starter-kit');

        $response->assertSuccessful();
        $response->assertSee('docker volume create node-binaries', false);
        $response->assertSee('node:24-slim', false);
        $response->assertSee('-v node-binaries:/usr/local/node:ro', false);
        $response->assertSee('export PATH=/usr/local/node/bin', false);
        $response->assertSee('docker volume rm node-binaries', false);
    });

    test('standard starter kits do not install node binaries', function () {
        $response = $this->get('/build?name=my-app&frontend=vue');

        $response->assertSuccessful();
        $response->assertDontSee('node-binaries', false);
        $response->assertDontSee('node:24-slim', false);
        $response->assertDontSee('/usr/local/node', false);
    });

    test('default build does not install node binaries', function () {
        $response = $this->get('/build?name=my-app&services=pgsql,redis');

        $response->assertSuccessful();
        $response->assertDontSee('node-binaries', false);
        $response->assertDontSee('node:24-slim', false);
        $response->assertDontSee('/usr/local/node', false);
    });

    test('different javascript runtimes can be picked', function (string $runtime) {
        $response = $this->get("/build?name=my-app&javascript={$runtime}");

        $response->assertSuccessful();
        $response->assertSee("--{$runtime}");
    })->with(['npm', 'pnpm', 'bun', 'yarn']);

    test('does not accept invalid javascript runtimes', function () {
        $response = $this->get('/build?name=my-app&javascript=invalid-runtime');

        $response->assertStatus(400);
        $response->assertSee('Invalid JavaScript runtime', false);
    });

    test('boost flag can be added', function () {
        $response = $this->get('/build?name=my-app&boost');

        $response->assertSuccessful();
        $response->assertSee('--boost');
        $response->assertDontSee('--no-boost');
    });

    test('no boost flag sends no-boost by default', function () {
        $response = $this->get('/build?name=my-app');

        $response->assertSuccessful();
        $response->assertSee('--no-boost');
        $response->assertDontSee('--boost ');
    });

    test('boost flag works with other options', function () {
        $response = $this->get('/build?name=my-app&services=redis&frontend=vue&boost');

        $response->assertSuccessful();
        $response->assertSee('--boost');
        $response->assertDontSee('--no-boost');
        $response->assertSee('--vue');
        $response->assertSee('--with=redis');
    });

    test('teams flag can be added', function () {
        $response = $this->get('/build?name=my-app&teams');

        $response->assertSuccessful();
        $response->assertSee('--teams');
    });

    test('teams flag works with other options', function () {
        $response = $this->get('/build?name=my-app&services=redis&frontend=vue&teams');

        $response->assertSuccessful();
        $response->assertSee('--teams');
        $response->assertSee('--vue');
        $response->assertSee('--with=redis');
    });

    test('teams is not added by default', function () {
        $response = $this->get('/build?name=my-app');

        $response->assertSuccessful();
        $response->assertDontSee('--teams');
    });

    test('javascript runtime works with other options', function () {
        $response = $this->get('/build?name=my-app&javascript=pnpm&frontend=react&services=redis');

        $response->assertSuccessful();
        $response->assertSee('--pnpm');
        $response->assertSee('--react');
        $response->assertSee('--with=redis');
    });

    test('no authentication can be picked', function () {
        $response = $this->get('/build?name=my-app&auth=no-authentication');

        $response->assertSuccessful();
        $response->assertSee('--no-authentication');
    });

    test('workos authentication can be picked', function () {
        $response = $this->get('/build?name=my-app&auth=workos');

        $response->assertSuccessful();
        $response->assertSee('--workos');
    });

    test('does not accept invalid auth provider', function () {
        $response = $this->get('/build?name=my-app&auth=invalid-auth');

        $response->assertStatus(400);
        $response->assertSee('Invalid authentication provider', false);
    });

    test('pest testing framework can be picked', function () {
        $response = $this->get('/build?name=my-app&testing=pest');

        $response->assertSuccessful();
        $response->assertSee('--pest');
    });

    test('phpunit testing framework can be picked', function () {
        $response = $this->get('/build?name=my-app&testing=phpunit');

        $response->assertSuccessful();
        $response->assertSee('--phpunit');
    });

    test('custom starter kit url is required when using custom frontend', function () {
        $response = $this->get('/build?name=my-app&frontend=custom');

        $response->assertStatus(400);
        $response->assertSee('A valid URL is required when using a custom starter kit', false);
    });

    test('does not accept invalid custom starter kit url', function () {
        $response = $this->get('/build?name=my-app&frontend=custom&using=not-a-url');

        $response->assertStatus(400);
        $response->assertSee('Invalid custom starter kit URL', false);
    });

    test('all new options work together', function () {
        $response = $this->get('/build?name=my-app&services=pgsql,redis&frontend=svelte&javascript=bun&boost&teams&auth=no-authentication');

        $response->assertSuccessful();
        $response->assertSee('--svelte');
        $response->assertSee('--bun');
        $response->assertSee('--boost');
        $response->assertSee('--teams');
        $response->assertSee('--no-authentication');
        $response->assertDontSee('--no-boost');
        $response->assertSee('sail:install --with=pgsql,redis --php=8.5');
    });

    test('does not accept invalid application name', function () {
        $response = $this->get('/build?name=invalid.name');

        $response->assertStatus(400);
        $response->assertSee('Invalid application name', false);
    });

    test('application name is required', function () {
        $response = $this->get('/build?services=redis');

        $response->assertStatus(400);
        $response->assertSee('Invalid application name', false);
    });

    test('application name is sanitized in the error output', function () {
        $response = $this->get('/build?name=evil$(whoami)evil');

        $response->assertStatus(400);
        $response->assertDontSee('$(whoami)', false);
    });

    test('devcontainer flag can be added', function () {
        $response = $this->get('/build?name=my-app&services=redis&devcontainer');

        $response->assertSuccessful();
        $response->assertSee('--devcontainer');
    });

    test('no-node flag can be added', function () {
        $response = $this->get('/build?name=my-app&no-node');

        $response->assertSuccessful();
        $response->assertSee('--no-node');
    });

    test('no-node is not added by default', function () {
        $response = $this->get('/build?name=my-app');

        $response->assertSuccessful();
        $response->assertDontSee('--no-node');
    });

    test('php version defaults to 8.5', function () {
        $response = $this->get('/build?name=my-app');

        $response->assertSuccessful();
        $response->assertSee('--php=8.5');
    });

    test('different php versions can be picked', function (string $version) {
        $response = $this->get("/build?name=my-app&php={$version}");

        $response->assertSuccessful();
        $response->assertSee("--php={$version}");
    })->with(['8.5', '8.4', '8.3']);

    test('does not accept invalid php version', function () {
        $response = $this->get('/build?name=my-app&php=7.4');

        $response->assertStatus(400);
        $response->assertSee('Invalid PHP version', false);
    });

    test('php version works with other options', function () {
        $response = $this->get('/build?name=my-app&services=redis&frontend=vue&php=8.3');

        $response->assertSuccessful();
        $response->assertSee('--php=8.3');
        $response->assertSee('--vue');
        $response->assertSee('--with=redis');
    });

    test('database flag is passed when a single database service is selected', function (string $service) {
        $response = $this->get("/build?name=my-app&services={$service},redis");

        $response->assertSuccessful();
        $response->assertSee("--database={$service}");
    })->with(['mysql', 'pgsql', 'mariadb']);

    test('database flag is not passed when multiple database services are selected', function () {
        $response = $this->get('/build?name=my-app&services=mysql,pgsql,redis');

        $response->assertSuccessful();
        $response->assertDontSee('--database=');
    });

    test('database flag is not passed when no database service is selected', function () {
        $response = $this->get('/build?name=my-app&services=redis,mailpit');

        $response->assertSuccessful();
        $response->assertDontSee('--database=');
    });

    test('database flag is not passed when services is none', function () {
        $response = $this->get('/build?name=my-app&services=none');

        $response->assertSuccessful();
        $response->assertDontSee('--database=');
    });

    test('stores anonymous stats on build request', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/build?name=my-app&services=redis,pgsql&frontend=react&javascript=bun&auth=laravel&testing=pest&php=8.5&boost');

        Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
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
                && $job->data['livewire_class_components'] === false;
        });
    });

    test('stores services in pivot table', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/build?name=my-app&services=redis,pgsql');

        Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
            return $job->services === ['redis', 'pgsql'];
        });
    });

    test('does not store name or custom url or ip', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/build?name=my-secret-app&services=redis&frontend=custom&using=https://example.com/kit');

        Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
            return $job->data['custom_starter_kit'] === true
                && $job->data['starter_kit'] === 'custom';
        });
    });

    test('stores none services without pivot entries', function () {
        Queue::fake();

        $this->withHeader('User-Agent', 'curl/8.5')
            ->get('/build?name=my-app&services=none');

        Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
            return $job->services === ['none'];
        });
    });

    describe('user agent filtering', function () {
        test('passes browser user agent to the job', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/134.0.0.0 Safari/537.36')
                ->get('/build?name=my-app');

            Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
                return str_contains($job->userAgent, 'Mozilla');
            });
        });

        test('passes crawler user agent to the job', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'Googlebot/2.1')
                ->get('/build?name=my-app');

            Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
                return str_contains($job->userAgent, 'Googlebot');
            });
        });

        test('passes curl user agent to the job', function () {
            Queue::fake();

            $this->withHeader('User-Agent', 'curl/8.5.0')
                ->get('/build?name=my-app');

            Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
                return $job->userAgent === 'curl/8.5.0';
            });
        });

        test('passes empty string for missing or empty user agent', function () {
            Queue::fake();

            $this->withHeader('User-Agent', '')
                ->get('/build?name=my-app');

            Queue::assertPushed(RecordBuildStat::class, function (RecordBuildStat $job) {
                return $job->userAgent === '';
            });
        });
    });
});
