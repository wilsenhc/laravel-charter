<?php

namespace App\Actions;

use Illuminate\Support\Facades\Blade;

class BuildApplicationScript
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): string
    {
        $name = $data['name'];
        $servicesArray = $data['services'];
        $frontend = $data['frontend'] ?? 'none';
        $auth = $data['auth'] ?? 'laravel';
        $testing = $data['testing'] ?? 'pest';
        $javascript = $data['javascript'] ?? null;
        $using = $data['using'] ?? null;
        $teams = $data['teams'] ?? false;
        $php = $data['php'] ?? '8.5';
        $database = $data['database'] ?? 'none';
        $boost = $data['boost'] ?? false;
        $noNode = $data['no-node'] ?? false;
        $devcontainer = $data['devcontainer'] ?? false;
        $livewireClassComponents = $data['livewire-class-components'] ?? false;

        $with = implode(',', $servicesArray);

        $servicesString = $servicesArray === ['none']
            ? 'none'
            : implode(' ', $servicesArray);

        $devcontainerFlag = $devcontainer ? '--devcontainer' : '';

        $frontendFlag = ($frontend && $frontend !== 'none' && $frontend !== 'custom')
            ? "--{$frontend}"
            : null;

        if ($frontendFlag === '--livewire' && $livewireClassComponents) {
            $frontendFlag = '--livewire --livewire-class-components';
        }

        $authFlag = $auth && $auth !== 'laravel' ? "--{$auth}" : null;

        $testFramework = $testing ? "--{$testing}" : null;

        $javascriptRuntime = $javascript ? "--{$javascript}" : null;

        $usingFlag = $using ? "--using=\"{$using}\"" : null;

        $teamsFlag = $teams ? '--teams' : null;

        $boostFlag = $boost ? '--boost' : '--no-boost';

        $noNodeFlag = $noNode ? '--no-node' : null;

        $databaseFlag = $database !== 'none' ? "--database={$database}" : null;

        $options = implode(' ', array_filter([
            $frontendFlag,
            $authFlag,
            $testFramework,
            $javascriptRuntime,
            $usingFlag,
            $teamsFlag,
            $boostFlag,
            $noNodeFlag,
            $databaseFlag,
        ]));

        $runtime = $javascript ?? 'npm';

        $installCmd = match ($runtime) {
            'bun' => 'bun install',
            'pnpm' => 'pnpm install',
            'yarn' => 'yarn install',
            default => 'npm install',
        };

        $devCmd = match ($runtime) {
            'bun' => 'bun run dev',
            'pnpm' => 'pnpm run dev',
            'yarn' => 'yarn dev',
            default => 'npm run dev',
        };

        return Blade::render(
            (string) file_get_contents(resource_path('stubs/build-application.sh')),
            [
                'name' => $name,
                'options' => $options,
                'with' => $with,
                'php' => $php,
                'services' => $servicesString,
                'devcontainer' => $devcontainerFlag,
                'isCustomStarterKit' => $frontend === 'custom',
                'installCmd' => $installCmd,
                'devCmd' => $devCmd,
            ],
        );
    }
}
