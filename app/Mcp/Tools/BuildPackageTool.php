<?php

namespace App\Mcp\Tools;

use App\Actions\BuildPackageScript;
use App\Enums\BuildOptions;
use App\Jobs\RecordPackageBuildStat;
use App\Mcp\Traits\DetectsMcpSource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Generates a bash script that scaffolds a new Laravel package. Accepts the package name, PHP version, features (config, routes, views, translations, migrations, assets, commands, facade, boost-skill), and optional metadata (author name/email, vendor namespace, class name, etc.). Returns a ready-to-run shell script.')]
#[IsReadOnly]
class BuildPackageTool extends Tool
{
    use DetectsMcpSource;

    public function handle(Request $request, BuildPackageScript $buildScript): Response
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'alpha_dash'],
            'php' => ['nullable', 'string', 'in:'.implode(',', BuildOptions::AvailablePhpVersions->values())],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'in:'.implode(',', BuildOptions::AvailablePackageFeatures->values())],
            'author_name' => ['nullable', 'string', 'max:255'],
            'author_email' => ['nullable', 'email', 'max:255'],
            'package_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/'],
            'package_name_human' => ['nullable', 'string', 'max:255'],
            'package_description' => ['nullable', 'string', 'max:500'],
            'vendor_namespace' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'class_name' => ['nullable', 'string', 'max:255', 'alpha_dash'],
        ], [
            'name.required' => 'A package name is required (alpha_dash characters only).',
            'name.alpha_dash' => 'The package name may only contain letters, numbers, dashes, and underscores.',
            'php.in' => 'Invalid PHP version. Supported versions are: '.implode(', ', BuildOptions::AvailablePhpVersions->values()).'.',
            'features.*.in' => 'Invalid feature. Supported features are: '.implode(', ', BuildOptions::AvailablePackageFeatures->values()).'.',
            'package_name.regex' => 'Package name must be in the format vendor/package.',
        ]);

        $mcpSource = $this->detectMcpSource();
        $validated['mcp_source'] = $mcpSource;

        $script = $buildScript->handle($validated);

        $features = $validated['features'] ?? [];
        $featureData = array_combine(
            $features,
            array_fill(0, count($features), true),
        );

        RecordPackageBuildStat::dispatch([
            'php_version' => $validated['php'] ?? '8.5',
            'config' => $featureData['config'] ?? false,
            'routes' => $featureData['routes'] ?? false,
            'views' => $featureData['views'] ?? false,
            'translations' => $featureData['translations'] ?? false,
            'migrations' => $featureData['migrations'] ?? false,
            'assets' => $featureData['assets'] ?? false,
            'commands' => $featureData['commands'] ?? false,
            'facade' => $featureData['facade'] ?? false,
            'boost_skill' => $featureData['boost-skill'] ?? false,
            'mcp_source' => $mcpSource,
        ]);

        return Response::text($script);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('The name of the Laravel package (alpha_dash characters only).')
                ->required(),
            'php' => $schema->string()
                ->description('The PHP version for the package scaffolding container.')
                ->enum(BuildOptions::AvailablePhpVersions->values())
                ->default('8.5'),
            'features' => $schema->array()
                ->items($schema->string()->description('A package feature name.'))
                ->description('Package features to include (config, routes, views, translations, migrations, assets, commands, facade, boost-skill).')
                ->enum([BuildOptions::AvailablePackageFeatures->values()])
                ->default([]),
            'author_name' => $schema->string()
                ->description('The author name for composer.json.'),
            'author_email' => $schema->string()
                ->description('The author email for composer.json.')
                ->format('email'),
            'package_name' => $schema->string()
                ->description('The Composer package name (vendor/package format).')
                ->pattern('^[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$'),
            'package_name_human' => $schema->string()
                ->description('The human-readable package display name.'),
            'package_description' => $schema->string()
                ->description('A short description of the package for composer.json.'),
            'vendor_namespace' => $schema->string()
                ->description('The vendor namespace for the package (alpha_dash).'),
            'class_name' => $schema->string()
                ->description('The base class name for the package service provider (alpha_dash).'),
        ];
    }
}
