<?php

namespace App\Http\Controllers;

use App\Actions\BuildPackageScriptAction;
use App\Http\Requests\BuildPackageRequest;
use App\Jobs\RecordPackageBuildStatJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Head\Facades\Head;
use Laravel\Head\Facades\Schema;

class BuildPackageController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $locale = app()->getLocale();

        Head::title(__('build.package.title'))
            ->description(__('build.package.meta_description'))
            ->canonical('/'.$locale.'/package');

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('build.package.title') => url($locale.'/package'),
        ]));

        return Inertia::render('Build/Package', [
            'url' => config('app.url'),
        ]);
    }

    public function show(BuildPackageRequest $request, BuildPackageScriptAction $buildScript): Response
    {
        $validated = $request->validated();

        $script = $buildScript($validated);

        if (! Str::contains($request->userAgent() ?? '', 'Mozilla')) {
            $features = $request->validated('features', []);
            $featureData = array_combine(
                $features,
                array_fill(0, count($features), true),
            );

            RecordPackageBuildStatJob::dispatch([
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
                'mcp_source' => 'web',
            ]);
        }

        return response($script, 200, [
            'Content-Type' => 'text/plain',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
