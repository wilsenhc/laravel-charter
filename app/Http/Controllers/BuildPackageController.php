<?php

namespace App\Http\Controllers;

use App\Actions\BuildPackageScript;
use App\Http\Requests\BuildPackageRequest;
use App\Jobs\RecordPackageBuildStat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BuildPackageController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Build/Package', [
            'url' => $request->root(),
        ]);
    }

    public function show(BuildPackageRequest $request, BuildPackageScript $buildScript): Response
    {
        $validated = $request->validated();

        $script = $buildScript->handle($validated);

        if (! Str::contains($request->userAgent() ?? '', 'Mozilla')) {
            $features = $request->validated('features', []);
            $featureData = array_combine(
                $features,
                array_fill(0, count($features), true),
            );

            RecordPackageBuildStat::dispatch([
                'php_version' => $validated['php'],
                'config' => $featureData['config'] ?? false,
                'routes' => $featureData['routes'] ?? false,
                'views' => $featureData['views'] ?? false,
                'translations' => $featureData['translations'] ?? false,
                'migrations' => $featureData['migrations'] ?? false,
                'assets' => $featureData['assets'] ?? false,
                'commands' => $featureData['commands'] ?? false,
                'facade' => $featureData['facade'] ?? false,
                'boost_skill' => $featureData['boost-skill'] ?? false,
            ]);
        }

        return response($script, 200, [
            'Content-Type' => 'text/plain',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
