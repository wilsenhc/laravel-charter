<?php

use App\Jobs\RecordApplicationBuildStat;
use App\Jobs\RecordPackageBuildStat;
use App\Mcp\Servers\CharterServer;
use App\Mcp\Tools\BuildApplicationTool;
use App\Mcp\Tools\BuildPackageTool;
use Illuminate\Support\Facades\Queue;

describe('build-application tool', function () {
    describe('validation', function () {
        test('name is required', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'services' => ['redis'],
            ]);

            $response->assertHasErrors();
            $response->assertHasErrors(['An application name is required']);
        });

        test('services is required', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
            ]);

            $response->assertHasErrors();
            $response->assertHasErrors(['At least one service must be specified']);
        });

        test('rejects invalid name', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'invalid name!',
                'services' => ['redis'],
            ]);

            $response->assertHasErrors();
            $response->assertHasErrors(['letters, numbers, dashes, and underscores']);
        });

        test('rejects invalid service name', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['invalid-service'],
            ]);

            $response->assertHasErrors();
        });

        test('rejects none with other services', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['none', 'redis'],
            ]);

            $response->assertHasErrors();
            $response->assertHasErrors(['Cannot use "none" with other services']);
        });

        test('rejects invalid frontend', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'frontend' => 'invalid',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid auth provider', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'auth' => 'invalid-auth',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid testing framework', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'testing' => 'invalid',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid javascript runtime', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'javascript' => 'invalid',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid php version', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'php' => '7.4',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid database driver', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'database' => 'invalid-db',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid teams value', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'teams' => 'not-a-boolean',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid boost value', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'boost' => 'not-a-boolean',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid custom starter kit url', function () {
            $response = CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'using' => 'not-a-url',
            ]);

            $response->assertHasErrors();
        });
    });

    test('generates a build script for a valid request', function () {
        Queue::fake();

        $response = CharterServer::tool(BuildApplicationTool::class, [
            'name' => 'my-app',
            'services' => ['pgsql', 'redis'],
        ]);

        $response->assertOk();
        $response->assertSee('laravel new my-app');
    });

    describe('stats', function () {
        test('records stats with all options', function () {
            Queue::fake();

            CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['pgsql', 'redis'],
                'frontend' => 'react',
                'auth' => 'laravel',
                'testing' => 'pest',
                'javascript' => 'bun',
                'php' => '8.5',
                'database' => 'pgsql',
                'teams' => true,
                'boost' => true,
                'devcontainer' => true,
                'no-node' => true,
            ]);

            Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
                return $job->data['php_version'] === '8.5'
                    && $job->data['starter_kit'] === 'react'
                    && $job->data['custom_starter_kit'] === false
                    && $job->data['javascript_runtime'] === 'bun'
                    && $job->data['auth_provider'] === 'laravel'
                    && $job->data['testing_framework'] === 'pest'
                    && $job->data['teams'] === true
                    && $job->data['boost'] === true
                    && $job->data['devcontainer'] === true
                    && $job->data['no_node'] === true
                    && $job->data['livewire_class_components'] === false
                    && $job->data['database_driver'] === 'pgsql'
                    && $job->services === ['pgsql', 'redis'];
            });
        });

        test('records defaults when no options provided', function () {
            Queue::fake();

            CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['none'],
            ]);

            Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
                return $job->data['php_version'] === '8.5'
                    && $job->data['starter_kit'] === 'none'
                    && $job->data['custom_starter_kit'] === false
                    && $job->data['javascript_runtime'] === null
                    && $job->data['auth_provider'] === 'laravel'
                    && $job->data['testing_framework'] === 'pest'
                    && $job->data['teams'] === false
                    && $job->data['boost'] === false
                    && $job->data['devcontainer'] === false
                    && $job->data['no_node'] === false
                    && $job->data['livewire_class_components'] === false
                    && $job->data['database_driver'] === null
                    && $job->services === ['none'];
            });
        });

        test('records custom starter kit flag', function () {
            Queue::fake();

            CharterServer::tool(BuildApplicationTool::class, [
                'name' => 'my-app',
                'services' => ['redis'],
                'frontend' => 'custom',
                'using' => 'https://example.com/kit',
            ]);

            Queue::assertPushed(RecordApplicationBuildStat::class, function (RecordApplicationBuildStat $job) {
                return $job->data['custom_starter_kit'] === true
                    && $job->data['starter_kit'] === 'custom';
            });
        });
    });
});

describe('build-package tool', function () {
    describe('validation', function () {
        test('name is required', function () {
            $response = CharterServer::tool(BuildPackageTool::class, []);

            $response->assertHasErrors();
            $response->assertHasErrors(['A package name is required']);
        });

        test('rejects invalid name', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'invalid name!',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid php version', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'php' => '7.4',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid feature', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'features' => ['invalid-feature'],
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid package name format', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'package_name' => 'invalid',
            ]);

            $response->assertHasErrors();
            $response->assertHasErrors(['vendor/package']);
        });

        test('rejects invalid author email', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'author_email' => 'not-an-email',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid vendor namespace', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'vendor_namespace' => 'invalid namespace!',
            ]);

            $response->assertHasErrors();
        });

        test('rejects invalid class name', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'class_name' => 'invalid class!',
            ]);

            $response->assertHasErrors();
        });

        test('rejects too long description', function () {
            $response = CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'package_description' => str_repeat('a', 501),
            ]);

            $response->assertHasErrors();
        });
    });

    test('generates a build script for a valid request', function () {
        Queue::fake();

        $response = CharterServer::tool(BuildPackageTool::class, [
            'name' => 'my-package',
        ]);

        $response->assertOk();
        $response->assertSee('laravel package my-package');
    });

    describe('stats', function () {
        test('records stats with all features', function () {
            Queue::fake();

            CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
                'features' => ['config', 'routes', 'views', 'translations', 'migrations', 'assets', 'commands', 'facade', 'boost-skill'],
                'php' => '8.5',
            ]);

            Queue::assertPushed(RecordPackageBuildStat::class, function (RecordPackageBuildStat $job) {
                return $job->data['php_version'] === '8.5'
                    && $job->data['config'] === true
                    && $job->data['routes'] === true
                    && $job->data['views'] === true
                    && $job->data['translations'] === true
                    && $job->data['migrations'] === true
                    && $job->data['assets'] === true
                    && $job->data['commands'] === true
                    && $job->data['facade'] === true
                    && $job->data['boost_skill'] === true;
            });
        });

        test('records no features when none selected', function () {
            Queue::fake();

            CharterServer::tool(BuildPackageTool::class, [
                'name' => 'my-package',
            ]);

            Queue::assertPushed(RecordPackageBuildStat::class, function (RecordPackageBuildStat $job) {
                return $job->data['php_version'] === '8.5'
                    && $job->data['config'] === false
                    && $job->data['routes'] === false
                    && $job->data['views'] === false
                    && $job->data['translations'] === false
                    && $job->data['migrations'] === false
                    && $job->data['assets'] === false
                    && $job->data['commands'] === false
                    && $job->data['facade'] === false
                    && $job->data['boost_skill'] === false;
            });
        });
    });
});
