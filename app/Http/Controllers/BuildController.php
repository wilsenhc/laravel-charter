<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildShowRequest;
use App\Jobs\RecordBuildStat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BuildController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Build/Index', [
            'url' => $request->root(),
        ]);
    }

    public function show(BuildShowRequest $request): Response
    {
        $name = $request->validated('name');
        $servicesArray = $request->validated('services');
        $frontend = $request->validated('frontend');
        $auth = $request->validated('auth');
        $testing = $request->validated('testing');
        $javascript = $request->validated('javascript');
        $using = $request->validated('using');
        $teams = $request->validated('teams');
        $php = $request->validated('php');
        $database = $request->validated('database');

        $with = implode(',', $servicesArray);

        $servicesString = $servicesArray === ['none']
            ? 'none'
            : implode(' ', $servicesArray);

        $devcontainer = $request->has('devcontainer') ? '--devcontainer' : '';

        $frontendFlag = ($frontend && $frontend !== 'none' && $frontend !== 'custom')
            ? "--{$frontend}"
            : null;

        $livewireClassComponents = $request->validated('livewire-class-components');

        if ($frontendFlag === '--livewire' && $livewireClassComponents) {
            $frontendFlag = '--livewire --livewire-class-components';
        }

        $authFlag = $auth && $auth !== 'laravel' ? "--{$auth}" : null;

        $testFramework = $testing ? "--{$testing}" : null;

        $javascriptRuntime = $javascript ? "--{$javascript}" : null;

        $usingFlag = $using ? "--using=\"{$using}\"" : null;

        $teamsFlag = $teams ? '--teams' : null;

        $boost = $request->has('boost') ? '--boost' : '--no-boost';

        $noNodeFlag = $request->validated('no-node') ? '--no-node' : null;

        $databaseFlag = $database !== 'none' ? "--database={$database}" : null;

        $options = implode(' ', array_filter([
            $frontendFlag,
            $authFlag,
            $testFramework,
            $javascriptRuntime,
            $usingFlag,
            $teamsFlag,
            $boost,
            $noNodeFlag,
            $databaseFlag,
        ]));

        $script = Blade::render(
            (string) file_get_contents(resource_path('stubs/build.sh')),
            [
                'name' => $name,
                'options' => $options,
                'with' => $with,
                'php' => $php,
                'services' => $servicesString,
                'devcontainer' => $devcontainer,
                'isCustomStarterKit' => $frontend === 'custom',
            ],
        );

        if (! Str::contains($request->userAgent() ?? '', 'Mozilla')) {
            RecordBuildStat::dispatch(
                data: [
                    'php_version' => $php,
                    'starter_kit' => $frontend ?? 'none',
                    'custom_starter_kit' => $frontend === 'custom',
                    'javascript_runtime' => $javascript,
                    'auth_provider' => $auth,
                    'testing_framework' => $testing,
                    'teams' => $teams,
                    'boost' => $request->has('boost'),
                    'devcontainer' => $request->has('devcontainer'),
                    'no_node' => $noNodeFlag !== null,
                    'livewire_class_components' => $livewireClassComponents,
                    'database_driver' => $database !== 'none' ? $database : null,
                ],
                services: $servicesArray,
            );
        }

        return response($script, 200, ['Content-Type' => 'text/plain']);
    }
}
