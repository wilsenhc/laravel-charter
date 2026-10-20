<?php

namespace App\Enums;

enum BuildOptions
{
    case AVAILABLE_SERVICES;
    case AVAILABLE_STARTER_KITS;
    case AVAILABLE_JAVASCRIPT_RUNTIMES;
    case AVAILABLE_AUTH_PROVIDERS;
    case AVAILABLE_TESTING_FRAMEWORKS;
    case AVAILABLE_PHP_VERSIONS;
    case AVAILABLE_DATABASE_DRIVERS;
    case AVAILABLE_PACKAGE_FEATURES;

    /**
     * @return array<string>
     */
    public function values(): array
    {
        return match ($this) {
            self::AVAILABLE_SERVICES => [
                'mysql',
                'mariadb',
                'pgsql',
                'mongodb',
                'redis',
                'valkey',
                'memcached',
                'meilisearch',
                'typesense',
                'rustfs',
                'mailpit',
                'mailtrap-local',
                'rabbitmq',
                'selenium',
                'soketi',
            ],
            self::AVAILABLE_STARTER_KITS => [
                'none',
                'api',
                'livewire',
                'vue',
                'react',
                'svelte',
                'custom',
            ],
            self::AVAILABLE_JAVASCRIPT_RUNTIMES => ['npm', 'pnpm', 'bun', 'yarn'],
            self::AVAILABLE_AUTH_PROVIDERS => ['no-authentication', 'laravel', 'workos'],
            self::AVAILABLE_TESTING_FRAMEWORKS => ['pest', 'phpunit'],
            self::AVAILABLE_PHP_VERSIONS => ['8.5', '8.4', '8.3'],
            self::AVAILABLE_DATABASE_DRIVERS => ['mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv'],
            self::AVAILABLE_PACKAGE_FEATURES => [
                'config',
                'routes',
                'views',
                'translations',
                'migrations',
                'assets',
                'commands',
                'facade',
                'boost-skill',
            ],
        };
    }

    /**
     * @return array<string, string> Display labels keyed by value for values that need one.
     */
    public function labels(): array
    {
        return match ($this) {
            self::AVAILABLE_SERVICES => [
                'mailtrap-local' => 'mailtrap',
            ],
            default => [],
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::AVAILABLE_SERVICES => 'availableServices',
            self::AVAILABLE_STARTER_KITS => 'availableStarterKits',
            self::AVAILABLE_JAVASCRIPT_RUNTIMES => 'availableJavascriptRuntimes',
            self::AVAILABLE_AUTH_PROVIDERS => 'availableAuthProviders',
            self::AVAILABLE_TESTING_FRAMEWORKS => 'availableTestingFrameworks',
            self::AVAILABLE_PHP_VERSIONS => 'availablePhpVersions',
            self::AVAILABLE_DATABASE_DRIVERS => 'availableDatabaseDrivers',
            self::AVAILABLE_PACKAGE_FEATURES => 'availablePackageFeatures',
        };
    }

    /**
     * @return array<string, array<string>|array<string, string>>
     */
    public static function all(): array
    {
        return [
            'availableServices' => self::AVAILABLE_SERVICES->values(),
            'serviceLabels' => self::AVAILABLE_SERVICES->labels(),
            'availableStarterKits' => self::AVAILABLE_STARTER_KITS->values(),
            'availableJavascriptRuntimes' => self::AVAILABLE_JAVASCRIPT_RUNTIMES->values(),
            'availableAuthProviders' => self::AVAILABLE_AUTH_PROVIDERS->values(),
            'availableTestingFrameworks' => self::AVAILABLE_TESTING_FRAMEWORKS->values(),
            'availablePhpVersions' => self::AVAILABLE_PHP_VERSIONS->values(),
            'availableDatabaseDrivers' => self::AVAILABLE_DATABASE_DRIVERS->values(),
            'availablePackageFeatures' => self::AVAILABLE_PACKAGE_FEATURES->values(),
        ];
    }

    /**
     * Extract a valid application-builder preset from arbitrary input (query
     * parameters, curated guide data), dropping unknown or unsupported values.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public static function applicationPreset(array $input): array
    {
        $preset = [];

        if (is_string($input['name'] ?? null) && preg_match('/^[a-zA-Z0-9_-]+$/', $input['name'])) {
            $preset['name'] = $input['name'];
        }

        $services = self::listValues($input['services'] ?? null, [...self::AVAILABLE_SERVICES->values(), 'none']);

        if ($services !== [] && ! (in_array('none', $services, true) && count($services) > 1)) {
            $preset['services'] = $services;
        }

        foreach ([
            'frontend' => self::AVAILABLE_STARTER_KITS,
            'auth' => self::AVAILABLE_AUTH_PROVIDERS,
            'testing' => self::AVAILABLE_TESTING_FRAMEWORKS,
            'javascript' => self::AVAILABLE_JAVASCRIPT_RUNTIMES,
            'php' => self::AVAILABLE_PHP_VERSIONS,
        ] as $key => $case) {
            if (is_string($input[$key] ?? null) && in_array($input[$key], $case->values(), true)) {
                $preset[$key] = $input[$key];
            }
        }

        if (is_string($input['database'] ?? null)
            && in_array($input['database'], [...self::AVAILABLE_DATABASE_DRIVERS->values(), 'none'], true)) {
            $preset['database'] = $input['database'];
        }

        if (is_string($input['using'] ?? null)
            && filter_var($input['using'], FILTER_VALIDATE_URL) !== false
            && ($input['frontend'] ?? null) === 'custom') {
            $preset['using'] = $input['using'];
        }

        foreach (['teams', 'boost', 'devcontainer', 'no-node', 'livewire-class-components'] as $flag) {
            if (isset($input[$flag])) {
                $preset[$flag] = true;
            }
        }

        return $preset;
    }

    /**
     * Extract a valid package-builder preset from arbitrary input.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public static function packagePreset(array $input): array
    {
        $preset = [];

        if (is_string($input['name'] ?? null) && preg_match('/^[a-zA-Z0-9_-]+$/', $input['name'])) {
            $preset['name'] = $input['name'];
        }

        if (is_string($input['php'] ?? null) && in_array($input['php'], self::AVAILABLE_PHP_VERSIONS->values(), true)) {
            $preset['php'] = $input['php'];
        }

        $features = self::listValues($input['features'] ?? null, self::AVAILABLE_PACKAGE_FEATURES->values());

        if ($features !== []) {
            $preset['features'] = $features;
        }

        return $preset;
    }

    /**
     * @param  array<string>  $allowed
     * @return array<string>
     */
    private static function listValues(mixed $value, array $allowed): array
    {
        $items = is_array($value)
            ? $value
            : (is_string($value) ? explode(',', $value) : []);

        return array_values(array_intersect(
            array_filter($items, is_string(...)),
            $allowed,
        ));
    }
}
