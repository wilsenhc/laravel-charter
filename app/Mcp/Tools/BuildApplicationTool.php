<?php

namespace App\Mcp\Tools;

use App\Actions\BuildApplicationScript;
use App\Enums\BuildOptions;
use App\Jobs\RecordApplicationBuildStat;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Generates a bash script that scaffolds a new Laravel application with Laravel Sail. Accepts project name, PHP version, Docker services, starter kit, authentication, testing framework, JavaScript runtime, database driver, and feature flags like teams, Boost, devcontainer, and no-node. Returns a ready-to-run script.')]
#[IsReadOnly]
class BuildApplicationTool extends Tool
{
    public function handle(Request $request, BuildApplicationScript $buildScript): Response
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash'],
            'services' => ['required', 'array'],
            'services.*' => ['string', 'in:'.implode(',', [...BuildOptions::AvailableServices->values(), 'none'])],
            'frontend' => ['nullable', 'string', 'in:'.implode(',', BuildOptions::AvailableStarterKits->values())],
            'auth' => ['nullable', 'string', 'in:'.implode(',', BuildOptions::AvailableAuthProviders->values())],
            'testing' => ['nullable', 'string', 'in:'.implode(',', BuildOptions::AvailableTestingFrameworks->values())],
            'javascript' => ['nullable', 'string', 'in:'.implode(',', BuildOptions::AvailableJavascriptRuntimes->values())],
            'php' => ['nullable', 'string', 'in:'.implode(',', BuildOptions::AvailablePhpVersions->values())],
            'database' => ['nullable', 'string', 'in:'.implode(',', [...BuildOptions::AvailableDatabaseDrivers->values(), 'none'])],
            'teams' => ['nullable', 'boolean'],
            'boost' => ['nullable', 'boolean'],
            'no-node' => ['nullable', 'boolean'],
            'devcontainer' => ['nullable', 'boolean'],
            'livewire-class-components' => ['nullable', 'boolean'],
            'using' => ['nullable', 'string', 'url'],
        ], [
            'name.required' => 'An application name is required (alpha_dash characters only).',
            'name.alpha_dash' => 'The application name may only contain letters, numbers, dashes, and underscores.',
            'services.required' => 'At least one service must be specified.',
            'services.*.in' => 'Invalid service name. Supported services are: '.implode(', ', BuildOptions::AvailableServices->values()).' or "none".',
            'frontend.in' => 'Invalid starter kit. Supported kits are: '.implode(', ', BuildOptions::AvailableStarterKits->values()).'.',
            'auth.in' => 'Invalid auth provider. Supported providers are: '.implode(', ', BuildOptions::AvailableAuthProviders->values()).'.',
            'testing.in' => 'Invalid testing framework. Supported frameworks are: '.implode(', ', BuildOptions::AvailableTestingFrameworks->values()).'.',
            'javascript.in' => 'Invalid JavaScript runtime. Supported runtimes are: '.implode(', ', BuildOptions::AvailableJavascriptRuntimes->values()).'.',
            'php.in' => 'Invalid PHP version. Supported versions are: '.implode(', ', BuildOptions::AvailablePhpVersions->values()).'.',
            'database.in' => 'Invalid database driver. Supported drivers are: '.implode(', ', BuildOptions::AvailableDatabaseDrivers->values()).' or "none".',
        ]);

        $data = $validated;

        if (in_array('none', $data['services'] ?? [], true) && count($data['services']) > 1) {
            return Response::error('Cannot use "none" with other services.');
        }

        $script = $buildScript->handle($data);

        RecordApplicationBuildStat::dispatch(
            data: [
                'php_version' => $data['php'] ?? '8.5',
                'starter_kit' => $data['frontend'] ?? 'none',
                'custom_starter_kit' => ($data['frontend'] ?? 'none') === 'custom',
                'javascript_runtime' => $data['javascript'] ?? null,
                'auth_provider' => $data['auth'] ?? 'laravel',
                'testing_framework' => $data['testing'] ?? 'pest',
                'teams' => $data['teams'] ?? false,
                'boost' => $data['boost'] ?? false,
                'devcontainer' => $data['devcontainer'] ?? false,
                'no_node' => $data['no-node'] ?? false,
                'livewire_class_components' => $data['livewire-class-components'] ?? false,
                'database_driver' => isset($data['database']) && $data['database'] !== 'none' ? $data['database'] : null,
            ],
            services: $data['services'],
        );

        return Response::text($script);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('The name of the Laravel application (alpha_dash characters only).')
                ->required(),
            'services' => $schema->array()
                ->items($schema->string()->description('A Docker service name.'))
                ->description('Docker services to include (e.g. mysql, pgsql, redis, meilisearch, mailpit, selenium, etc.).')
                ->enum([BuildOptions::AvailableServices->values()])
                ->default([]),
            'frontend' => $schema->string()
                ->description('The starter kit to use.')
                ->enum(BuildOptions::AvailableStarterKits->values())
                ->default('none'),
            'auth' => $schema->string()
                ->description('The authentication provider.')
                ->enum(BuildOptions::AvailableAuthProviders->values())
                ->default('laravel'),
            'testing' => $schema->string()
                ->description('The testing framework.')
                ->enum(BuildOptions::AvailableTestingFrameworks->values())
                ->default('pest'),
            'javascript' => $schema->string()
                ->description('The JavaScript runtime / package manager.')
                ->enum(BuildOptions::AvailableJavascriptRuntimes->values()),
            'php' => $schema->string()
                ->description('The PHP version for Sail.')
                ->enum(BuildOptions::AvailablePhpVersions->values())
                ->default('8.5'),
            'database' => $schema->string()
                ->description('The default database driver.')
                ->enum([...BuildOptions::AvailableDatabaseDrivers->values(), 'none'])
                ->default('none'),
            'teams' => $schema->boolean()
                ->description('Include Laravel teams support.')
                ->default(false),
            'boost' => $schema->boolean()
                ->description('Install Laravel Boost for AI-powered MCP helpers.')
                ->default(false),
            'no-node' => $schema->boolean()
                ->description('Skip Node.js installation.')
                ->default(false),
            'devcontainer' => $schema->boolean()
                ->description('Generate a Devcontainer configuration.')
                ->default(false),
            'livewire-class-components' => $schema->boolean()
                ->description('Use Livewire class components instead of single-file components (only applies when frontend is livewire).')
                ->default(false),
            'using' => $schema->string()
                ->description('URL for a custom starter kit (only used when frontend is "custom").')
                ->format('uri'),
        ];
    }
}
