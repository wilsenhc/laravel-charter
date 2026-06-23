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
        $teams = $request->has('teams');

        $with = implode(',', $servicesArray);

        $servicesString = $servicesArray === ['none']
            ? 'none'
            : implode(' ', $servicesArray);

        $devcontainer = $request->has('devcontainer') ? '--devcontainer' : '';

        $teamsFlag = $teams ? '--teams ' : '';

        $boost = $request->has('boost') ? '--boost ' : '--no-boost ';

        $frontendFlag = ($frontend && $frontend !== 'none' && $frontend !== 'custom')
            ? "--{$frontend} "
            : null;

        $authFlag = $auth ? "--{$auth} " : null;

        $testFramework = $testing ? "--{$testing}" : null;

        $javascriptRuntime = $javascript ? "--{$javascript} " : null;

        $usingFlag = $using ? "--using=\"{$using}\" " : null;

        $script = str_replace(
            ['{{ name }}', '{{ frontend }} ', '{{ authProvider }} ', '{{ testFramework }}', '{{ javascriptRuntime }}', '{{ using }}', '{{ teams }}', '{{ boost }}', '{{ with }}', '{{ services }}', '{{ devcontainer }}'],
            [$name, "$frontendFlag", "$authFlag", "$testFramework", "$javascriptRuntime", "$usingFlag", "$teamsFlag", "$boost", $with, $servicesString, $devcontainer],
            file_get_contents(resource_path('stubs/build.sh')),
        );

        return response($script, 200, ['Content-Type' => 'text/plain']);
    }
}
