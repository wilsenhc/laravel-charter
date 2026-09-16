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
                'minio',
                'rustfs',
                'mailpit',
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
     * @return array<string, array<string>>
     */
    public static function all(): array
    {
        return [
            'availableServices' => self::AVAILABLE_SERVICES->values(),
            'availableStarterKits' => self::AVAILABLE_STARTER_KITS->values(),
            'availableJavascriptRuntimes' => self::AVAILABLE_JAVASCRIPT_RUNTIMES->values(),
            'availableAuthProviders' => self::AVAILABLE_AUTH_PROVIDERS->values(),
            'availableTestingFrameworks' => self::AVAILABLE_TESTING_FRAMEWORKS->values(),
            'availablePhpVersions' => self::AVAILABLE_PHP_VERSIONS->values(),
            'availableDatabaseDrivers' => self::AVAILABLE_DATABASE_DRIVERS->values(),
            'availablePackageFeatures' => self::AVAILABLE_PACKAGE_FEATURES->values(),
        ];
    }
}
