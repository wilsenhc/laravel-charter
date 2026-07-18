<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildPackageRequest;
use App\Jobs\RecordPackageBuildStat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
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

    public function show(BuildPackageRequest $request): Response
    {
        $name = $request->validated('name');
        $php = $request->validated('php');
        $features = $request->validated('features', []);

        $featureFlags = array_map(fn (string $feature) => "--{$feature}", $features);

        $metadataFlags = [];

        if ($authorName = $request->validated('author_name')) {
            $metadataFlags[] = '--author-name=\\"'.$authorName.'\\"';
        }

        if ($authorEmail = $request->validated('author_email')) {
            $metadataFlags[] = '--author-email=\\"'.$authorEmail.'\\"';
        }

        if ($packageName = $request->validated('package_name')) {
            $metadataFlags[] = '--package-name=\\"'.$packageName.'\\"';
        }

        if ($packageNameHuman = $request->validated('package_name_human')) {
            $metadataFlags[] = '--package-name-human=\\"'.$packageNameHuman.'\\"';
        }

        if ($packageDescription = $request->validated('package_description')) {
            $metadataFlags[] = '--package-description=\\"'.$packageDescription.'\\"';
        }

        if ($vendorNamespace = $request->validated('vendor_namespace')) {
            $metadataFlags[] = '--vendor-namespace=\\"'.$vendorNamespace.'\\"';
        }

        if ($className = $request->validated('class_name')) {
            $metadataFlags[] = '--class-name=\\"'.$className.'\\"';
        }

        $options = implode(' ', [
            ...$featureFlags,
            ...$metadataFlags,
        ]);

        $script = Blade::render(
            (string) file_get_contents(resource_path('stubs/build-package.sh')),
            [
                'name' => $name,
                'options' => $options,
                'php' => $php,
            ],
        );

        if (! Str::contains($request->userAgent() ?? '', 'Mozilla')) {
            $featureData = array_combine(
                $features,
                array_fill(0, count($features), true),
            );

            RecordPackageBuildStat::dispatch([
                'php_version' => $php,
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

        return response($script, 200, ['Content-Type' => 'text/plain']);
    }
}
