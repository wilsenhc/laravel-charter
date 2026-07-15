<?php

namespace App\Enums;

enum BuildOptions
{
    case AvailableServices;
    case AvailableStarterKits;
    case AvailableJavascriptRuntimes;
    case AvailableAuthProviders;
    case AvailableTestingFrameworks;
    case AvailablePhpVersions;
    case AvailableDatabaseDrivers;

    /**
     * @return array<string>
     */
    public function values(): array
    {
        return match ($this) {
            self::AvailableServices => [
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
            self::AvailableStarterKits => [
                'none',
                'livewire',
                'vue',
                'react',
                'svelte',
                'custom',
            ],
            self::AvailableJavascriptRuntimes => ['npm', 'pnpm', 'bun', 'yarn'],
            self::AvailableAuthProviders => ['no-authentication', 'laravel', 'workos'],
            self::AvailableTestingFrameworks => ['pest', 'phpunit'],
            self::AvailablePhpVersions => ['8.5', '8.4', '8.3'],
            self::AvailableDatabaseDrivers => ['mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv'],
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::AvailableServices => 'availableServices',
            self::AvailableStarterKits => 'availableStarterKits',
            self::AvailableJavascriptRuntimes => 'availableJavascriptRuntimes',
            self::AvailableAuthProviders => 'availableAuthProviders',
            self::AvailableTestingFrameworks => 'availableTestingFrameworks',
            self::AvailablePhpVersions => 'availablePhpVersions',
            self::AvailableDatabaseDrivers => 'availableDatabaseDrivers',
        };
    }

    /**
     * @return array<string, array<string>>
     */
    public static function all(): array
    {
        return [
            'availableServices' => self::AvailableServices->values(),
            'availableStarterKits' => self::AvailableStarterKits->values(),
            'availableJavascriptRuntimes' => self::AvailableJavascriptRuntimes->values(),
            'availableAuthProviders' => self::AvailableAuthProviders->values(),
            'availableTestingFrameworks' => self::AvailableTestingFrameworks->values(),
            'availablePhpVersions' => self::AvailablePhpVersions->values(),
            'availableDatabaseDrivers' => self::AvailableDatabaseDrivers->values(),
        ];
    }
}
