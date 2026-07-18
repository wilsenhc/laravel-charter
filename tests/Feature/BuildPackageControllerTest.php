<?php

use App\Jobs\RecordPackageBuildStat;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

describe('index', function () {
    test('renders the build package page', function () {
        $this->get(route('build.package.index'))
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

    test('features can be included', function () {
        $response = $this->get('/package/build?name=my-package&features=config,routes');

        $response->assertSuccessful();
        $response->assertSee('--config');
        $response->assertSee('--routes');
    });

    test('features are not added by default', function () {
        $response = $this->get('/package/build?name=my-package');

        $response->assertSuccessful();
        $response->assertDontSee('--config');
        $response->assertDontSee('--routes');
    });

    test('single feature can be included', function () {
        $response = $this->get('/package/build?name=my-package&features=views');

        $response->assertSuccessful();
        $response->assertSee('--views');
    });

    test('does not accept invalid features', function () {
        $response = $this->get('/package/build?name=my-package&features=invalid-feature');

        $response->assertStatus(400);
        $response->assertSee('Invalid feature', false);
    });

    test('metadata fields can be filled', function () {
        $response = $this->get('/package/build?name=my-package&author_name=John&author_email=john@example.com&package_name=vendor/my-package&package_name_human=My+Package&package_description=A+great+package&vendor_namespace=Vendor&class_name=MyPackage');

        $response->assertSuccessful();
        $response->assertSee('--author-name="John"', false);
        $response->assertSee('--author-email="john@example.com"', false);
        $response->assertSee('--package-name="vendor/my-package"', false);
        $response->assertSee('--package-name-human="My Package"', false);
        $response->assertSee('--package-description="A great package"', false);
        $response->assertSee('--vendor-namespace="Vendor"', false);
        $response->assertSee('--class-name="MyPackage"', false);
    });

    test('metadata fields are not included by default', function () {
        $response = $this->get('/package/build?name=my-package');

        $response->assertSuccessful();
        $response->assertDontSee('--author-name');
        $response->assertDontSee('--author-email');
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

    test('php version defaults to 8.5', function () {
        $response = $this->get('/package/build?name=my-package');

        $response->assertSuccessful();
        $response->assertSee('php:8.5-cli');
    });

    test('different php versions can be picked', function (string $version) {
        $response = $this->get("/package/build?name=my-package&php={$version}");

        $response->assertSuccessful();
        $response->assertSee("php:{$version}-cli");
    })->with(['8.5', '8.4', '8.3']);

    test('does not accept invalid php version', function () {
        $response = $this->get('/package/build?name=my-package&php=7.4');

        $response->assertStatus(400);
        $response->assertSee('Invalid PHP version', false);
    });

    test('all options work together', function () {
        $response = $this->get('/package/build?name=my-package&features=config,routes,views&author_name=John&php=8.4');

        $response->assertSuccessful();
        $response->assertSee('--config');
        $response->assertSee('--routes');
        $response->assertSee('--views');
        $response->assertSee('--author-name="John"', false);
        $response->assertSee('php:8.4-cli');
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
                && $job->data['boost_skill'] === false;
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
                && $job->data['boost_skill'] === false;
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
