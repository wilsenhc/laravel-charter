<?php

namespace App\Http\Controllers;

use App\Actions\BuildApplicationScript;
use App\Http\Requests\BuildShowRequest;
use App\Jobs\RecordApplicationBuildStat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BuildApplicationController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Build/Application', [
            'url' => $request->root(),
        ]);
    }

    public function show(BuildShowRequest $request, BuildApplicationScript $buildScript): Response
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'services' => $validated['services'],
            'frontend' => $validated['frontend'] ?? 'none',
            'auth' => $validated['auth'] ?? 'laravel',
            'testing' => $validated['testing'] ?? 'pest',
            'php' => $validated['php'] ?? '8.5',
            'database' => $validated['database'] ?? 'none',
            'javascript' => $validated['javascript'] ?? null,
            'using' => $validated['using'] ?? null,
            'teams' => $validated['teams'] ?? false,
            'no-node' => $validated['no-node'] ?? false,
            'livewire-class-components' => $validated['livewire-class-components'] ?? false,
            'boost' => $request->has('boost'),
            'devcontainer' => $request->has('devcontainer'),
        ];

        $script = $buildScript->handle($data);

        if (! Str::contains($request->userAgent() ?? '', 'Mozilla')) {
            RecordApplicationBuildStat::dispatch(
                data: [
                    'php_version' => $data['php'],
                    'starter_kit' => $data['frontend'],
                    'custom_starter_kit' => $data['frontend'] === 'custom',
                    'javascript_runtime' => $data['javascript'],
                    'auth_provider' => $data['auth'],
                    'testing_framework' => $data['testing'],
                    'teams' => $data['teams'],
                    'boost' => $data['boost'],
                    'devcontainer' => $data['devcontainer'],
                    'no_node' => $data['no-node'],
                    'livewire_class_components' => $data['livewire-class-components'],
                    'database_driver' => $data['database'] !== 'none' ? $data['database'] : null,
                ],
                services: $data['services'],
            );
        }

        return response($script, 200, [
            'Content-Type' => 'text/plain',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
