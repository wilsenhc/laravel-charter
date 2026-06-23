<?php

namespace App\Enums;

enum BuildOptions
{
    case AvailableServices;
    case AvailableStarterKits;
    case AvailableJavascriptRuntimes;
    case AvailableAuthProviders;
    case AvailableTestingFrameworks;

    public function values(): array
    {
        return match ($this) {
            self::AvailableServices => [
                'mysql',
                'pgsql',
                'mariadb',
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
                'livewire-class-components',
                'vue',
                'react',
                'svelte',
                'custom',
            ],
            self::AvailableJavascriptRuntimes => ['npm', 'pnpm', 'bun', 'yarn'],
            self::AvailableAuthProviders => ['no-authentication', 'laravel', 'workos'],
            self::AvailableTestingFrameworks => ['pest', 'phpunit'],
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
        };
    }

    public static function all(): array
    {
        return [
            'availableServices' => self::AvailableServices->values(),
            'availableStarterKits' => self::AvailableStarterKits->values(),
            'availableJavascriptRuntimes' => self::AvailableJavascriptRuntimes->values(),
            'availableAuthProviders' => self::AvailableAuthProviders->values(),
            'availableTestingFrameworks' => self::AvailableTestingFrameworks->values(),
        ];
    }
}
