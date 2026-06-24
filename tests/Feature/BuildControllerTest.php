<?php

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
        $response = $this->get('/my-app?services=pgsql,redis');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('laravel new my-app');
        $response->assertSee('sail:install --with=pgsql,redis --php=8.5');
    });

    test('allows none as the only service', function () {
        $response = $this->get('/my-app?services=none');

        $response->assertSuccessful();
        $response->assertSee('--with=none');
    });

    test('does not accept none with other services', function () {
        $response = $this->get('/my-app?services=none,redis');

        $response->assertStatus(400);
        $response->assertSee('Invalid service name', false);
    });

    test('does not accept invalid services', function () {
        $response = $this->get('/my-app?services=invalid-service');

        $response->assertStatus(400);
        $response->assertSee('Invalid service name', false);
    });

    test('livewire starter kit can be picked', function () {
        $response = $this->get('/my-app?frontend=livewire');

        $response->assertSuccessful();
        $response->assertSee('--livewire');
    });

    test('livewire class components starter kit can be picked', function () {
        $response = $this->get('/my-app?frontend=livewire-class-components');

        $response->assertSuccessful();
        $response->assertSee('--livewire-class-components');
    });

    test('react starter kit can be picked', function () {
        $response = $this->get('/my-app?frontend=react');

        $response->assertSuccessful();
        $response->assertSee('--react');
    });

    test('vue starter kit can be picked', function () {
        $response = $this->get('/my-app?frontend=vue');

        $response->assertSuccessful();
        $response->assertSee('--vue');
    });

    test('svelte starter kit can be picked', function () {
        $response = $this->get('/my-app?frontend=svelte');

        $response->assertSuccessful();
        $response->assertSee('--svelte');
    });

    test('custom starter kit does not add a frontend flag', function () {
        $response = $this->get('/my-app?frontend=custom&using=https://example.com/starter-kit');

        $response->assertSuccessful();
        $response->assertDontSee('--custom');
        $response->assertSee('--using="https://example.com/starter-kit"', false);
    });

    test('different javascript runtimes can be picked', function (string $runtime) {
        $response = $this->get("/my-app?javascript={$runtime}");

        $response->assertSuccessful();
        $response->assertSee("--{$runtime}");
    })->with(['npm', 'pnpm', 'bun', 'yarn']);

    test('does not accept invalid javascript runtimes', function () {
        $response = $this->get('/my-app?javascript=invalid-runtime');

        $response->assertStatus(400);
        $response->assertSee('Invalid JavaScript runtime', false);
    });

    test('boost flag can be added', function () {
        $response = $this->get('/my-app?boost');

        $response->assertSuccessful();
        $response->assertSee('--boost');
        $response->assertDontSee('--no-boost');
    });

    test('no boost flag sends no-boost by default', function () {
        $response = $this->get('/my-app');

        $response->assertSuccessful();
        $response->assertSee('--no-boost');
        $response->assertDontSee('--boost ');
    });

    test('boost flag works with other options', function () {
        $response = $this->get('/my-app?services=redis&frontend=vue&boost');

        $response->assertSuccessful();
        $response->assertSee('--boost');
        $response->assertDontSee('--no-boost');
        $response->assertSee('--vue');
        $response->assertSee('--with=redis');
    });

    test('teams flag can be added', function () {
        $response = $this->get('/my-app?teams');

        $response->assertSuccessful();
        $response->assertSee('--teams');
    });

    test('teams flag works with other options', function () {
        $response = $this->get('/my-app?services=redis&frontend=vue&teams');

        $response->assertSuccessful();
        $response->assertSee('--teams');
        $response->assertSee('--vue');
        $response->assertSee('--with=redis');
    });

    test('teams is not added by default', function () {
        $response = $this->get('/my-app');

        $response->assertSuccessful();
        $response->assertDontSee('--teams');
    });

    test('javascript runtime works with other options', function () {
        $response = $this->get('/my-app?javascript=pnpm&frontend=react&services=redis');

        $response->assertSuccessful();
        $response->assertSee('--pnpm');
        $response->assertSee('--react');
        $response->assertSee('--with=redis');
    });

    test('no authentication can be picked', function () {
        $response = $this->get('/my-app?auth=no-authentication');

        $response->assertSuccessful();
        $response->assertSee('--no-authentication');
    });

    test('workos authentication can be picked', function () {
        $response = $this->get('/my-app?auth=workos');

        $response->assertSuccessful();
        $response->assertSee('--workos');
    });

    test('does not accept invalid auth provider', function () {
        $response = $this->get('/my-app?auth=invalid-auth');

        $response->assertStatus(400);
        $response->assertSee('Invalid authentication provider', false);
    });

    test('pest testing framework can be picked', function () {
        $response = $this->get('/my-app?testing=pest');

        $response->assertSuccessful();
        $response->assertSee('--pest');
    });

    test('phpunit testing framework can be picked', function () {
        $response = $this->get('/my-app?testing=phpunit');

        $response->assertSuccessful();
        $response->assertSee('--phpunit');
    });

    test('custom starter kit url is required when using custom frontend', function () {
        $response = $this->get('/my-app?frontend=custom');

        $response->assertStatus(400);
        $response->assertSee('A valid URL is required when using a custom starter kit', false);
    });

    test('does not accept invalid custom starter kit url', function () {
        $response = $this->get('/my-app?frontend=custom&using=not-a-url');

        $response->assertStatus(400);
        $response->assertSee('Invalid custom starter kit URL', false);
    });

    test('all new options work together', function () {
        $response = $this->get('/my-app?services=pgsql,redis&frontend=svelte&javascript=bun&boost&teams&auth=no-authentication');

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
        $response = $this->get('/invalid name');

        $response->assertStatus(404);
    });

    test('devcontainer flag can be added', function () {
        $response = $this->get('/my-app?services=redis&devcontainer');

        $response->assertSuccessful();
        $response->assertSee('--devcontainer');
    });

    test('php version defaults to 8.5', function () {
        $response = $this->get('/my-app');

        $response->assertSuccessful();
        $response->assertSee('--php=8.5');
    });

    test('different php versions can be picked', function (string $version) {
        $response = $this->get("/my-app?php={$version}");

        $response->assertSuccessful();
        $response->assertSee("--php={$version}");
    })->with(['8.5', '8.4', '8.3']);

    test('does not accept invalid php version', function () {
        $response = $this->get('/my-app?php=7.4');

        $response->assertStatus(400);
        $response->assertSee('Invalid PHP version', false);
    });

    test('php version works with other options', function () {
        $response = $this->get('/my-app?services=redis&frontend=vue&php=8.3');

        $response->assertSuccessful();
        $response->assertSee('--php=8.3');
        $response->assertSee('--vue');
        $response->assertSee('--with=redis');
    });
});
