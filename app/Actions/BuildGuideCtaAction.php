<?php

namespace App\Actions;

use App\Enums\BuildOptions;
use App\Models\Guide;

class BuildGuideCtaAction
{
    /**
     * Build the preselected builder link from the guide's curated parameters.
     * Values are validated against BuildOptions first, so an edited seed can
     * never produce a link with unsupported options.
     *
     * @return array{type: 'application'|'package', href: string}
     */
    public function __invoke(Guide $guide, string $locale): array
    {
        $builderParams = $guide->builder_params ?? [];

        if ($guide->category === 'Package') {
            $presets = BuildOptions::packagePreset($builderParams);

            $query = [
                'name' => $guide->app_name,
            ];

            if (isset($presets['php'])) {
                $query['php'] = $presets['php'];
            }

            if (! empty($presets['features'])) {
                $query['features'] = implode(',', $presets['features']);
            }

            return [
                'type' => 'package',
                'href' => '/'.$locale.'/package?'.http_build_query($query),
            ];
        }

        $presets = BuildOptions::applicationPreset($builderParams);

        $query = [
            'name' => $guide->app_name,
        ];

        if (! empty($presets['services'])) {
            $query['services'] = implode(',', $presets['services']);
        }

        foreach (['frontend', 'auth', 'testing', 'javascript', 'php', 'database'] as $key) {
            if (isset($presets[$key])) {
                $query[$key] = $presets[$key];
            }
        }

        foreach (['teams', 'boost', 'devcontainer', 'no-node', 'livewire-class-components'] as $flag) {
            if (! empty($presets[$flag])) {
                $query[$flag] = '1';
            }
        }

        if (isset($presets['using'])) {
            $query['using'] = $presets['using'];
        }

        return [
            'type' => 'application',
            'href' => '/'.$locale.'/application?'.http_build_query($query),
        ];
    }
}
