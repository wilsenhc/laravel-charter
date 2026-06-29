<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildShowRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function show(BuildShowRequest $request, string $name): Response
    {
        $servicesArray = $request->validated('services');
        $frontend = $request->validated('frontend');
        $auth = $request->validated('auth');
        $testing = $request->validated('testing');
        $javascript = $request->validated('javascript');
        $using = $request->validated('using');
        $teams = $request->validated('teams');
        $php = $request->validated('php');

        $with = implode(',', $servicesArray);

        $servicesString = $servicesArray === ['none']
            ? 'none'
            : implode(' ', $servicesArray);

        $devcontainer = $request->has('devcontainer') ? '--devcontainer' : '';

        $frontendFlag = ($frontend && $frontend !== 'none' && $frontend !== 'custom')
            ? "--{$frontend}"
            : null;

        $authFlag = $auth ? "--{$auth}" : null;

        $testFramework = $testing ? "--{$testing}" : null;

        $javascriptRuntime = $javascript ? "--{$javascript}" : null;

        $usingFlag = $using ? "--using=\"{$using}\"" : null;

        $teamsFlag = $teams ? '--teams' : null;

        $boost = $request->has('boost') ? '--boost' : '--no-boost';

        $databaseServices = ['mysql', 'pgsql', 'mariadb'];
        $selectedDatabases = array_values(array_intersect($servicesArray, $databaseServices));
        $databaseFlag = count($selectedDatabases) === 1 ? "--database={$selectedDatabases[0]}" : null;

        $options = implode(' ', array_filter([
            $frontendFlag,
            $authFlag,
            $testFramework,
            $javascriptRuntime,
            $usingFlag,
            $teamsFlag,
            $boost,
            $databaseFlag,
        ]));

        // Node.js is only required when a custom starter kit is used (via npx)...
        $isCustomStarterKit = $frontend === 'custom';

        $nodeSetup = $isCustomStarterKit
            ? "# Extract Node.js from the official Node image (needed for custom starter kits via npx)...\ndocker volume create node-binaries >/dev/null 2>&1\ndocker run --rm -v node-binaries:/out node:24-slim cp -a /usr/local/bin /usr/local/lib /out/"
            : '';

        $nodeMount = $isCustomStarterKit ? "    -v node-binaries:/usr/local/node:ro \\\n" : '';

        $nodePath = $isCustomStarterKit ? 'export PATH=/usr/local/node/bin:\$PATH && ' : '';

        $nodeCleanup = $isCustomStarterKit ? 'docker volume rm node-binaries >/dev/null 2>&1' : '';

        $script = str_replace(
            ['{{ name }}', '{{ options }}', '{{ with }}', '{{ php }}', '{{ services }}', '{{ devcontainer }}', '{{ node_setup }}', '{{ node_mount }}', '{{ node_path }}', '{{ node_cleanup }}'],
            [$name, $options, $with, $php, $servicesString, $devcontainer, $nodeSetup, $nodeMount, $nodePath, $nodeCleanup],
            (string) file_get_contents(resource_path('stubs/build.sh')),
        );

        return response($script, 200, ['Content-Type' => 'text/plain']);
    }
}
