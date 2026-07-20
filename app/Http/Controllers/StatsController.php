<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStat;
use App\Models\PackageStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StatsController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $appQuery = ApplicationStat::query();
        $packageQuery = PackageStat::query();

        $from = $request->query('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        $appQuery->whereDate('application_stats.created_at', '>=', $from);
        $packageQuery->whereDate('package_stats.created_at', '>=', $from);

        if ($to) {
            $appQuery->whereDate('application_stats.created_at', '<=', $to);
            $packageQuery->whereDate('package_stats.created_at', '<=', $to);
        }

        // App stats...
        $phpVersions = (clone $appQuery)
            ->selectRaw('php_version, count(*) as count')
            ->groupBy('php_version')
            ->orderByDesc('count')
            ->pluck('count', 'php_version');

        $services = (clone $appQuery)
            ->join('application_stat_services', 'application_stats.id', '=', 'application_stat_services.stat_id')
            ->join('application_services', 'application_stat_services.service_id', '=', 'application_services.id')
            ->selectRaw('application_services.name, count(*) as count')
            ->groupBy('application_services.name')
            ->orderByDesc('count')
            ->pluck('count', 'name');

        $starterKits = (clone $appQuery)
            ->selectRaw('starter_kit, count(*) as count')
            ->groupBy('starter_kit')
            ->orderByDesc('count')
            ->pluck('count', 'starter_kit');

        $javascriptRuntimes = (clone $appQuery)
            ->whereNotNull('javascript_runtime')
            ->selectRaw('javascript_runtime, count(*) as count')
            ->groupBy('javascript_runtime')
            ->orderByDesc('count')
            ->pluck('count', 'javascript_runtime');

        $authProviders = (clone $appQuery)
            ->whereNotNull('auth_provider')
            ->selectRaw('auth_provider, count(*) as count')
            ->groupBy('auth_provider')
            ->orderByDesc('count')
            ->pluck('count', 'auth_provider');

        $testingFrameworks = (clone $appQuery)
            ->selectRaw('testing_framework, count(*) as count')
            ->groupBy('testing_framework')
            ->orderByDesc('count')
            ->pluck('count', 'testing_framework');

        $databaseDrivers = (clone $appQuery)
            ->whereNotNull('database_driver')
            ->selectRaw('database_driver, count(*) as count')
            ->groupBy('database_driver')
            ->orderByDesc('count')
            ->pluck('count', 'database_driver');

        $booleanOptions = (clone $appQuery)->toBase()->select(
            DB::raw('SUM(CASE WHEN teams THEN 1 ELSE 0 END) as teams'),
            DB::raw('SUM(CASE WHEN boost THEN 1 ELSE 0 END) as boost'),
            DB::raw('SUM(CASE WHEN devcontainer THEN 1 ELSE 0 END) as devcontainer'),
            DB::raw('SUM(CASE WHEN no_node THEN 1 ELSE 0 END) as no_node'),
            DB::raw('SUM(CASE WHEN livewire_class_components THEN 1 ELSE 0 END) as livewire_class_components'),
            DB::raw('SUM(CASE WHEN custom_starter_kit THEN 1 ELSE 0 END) as custom_starter_kit'),
        )->first();

        $totalApps = $appQuery->count();

        // Package stats...
        $packagePhpVersions = (clone $packageQuery)
            ->selectRaw('php_version, count(*) as count')
            ->groupBy('php_version')
            ->orderByDesc('count')
            ->pluck('count', 'php_version');

        $packageFeatureOptions = (clone $packageQuery)->toBase()->select(
            DB::raw('SUM(CASE WHEN config THEN 1 ELSE 0 END) as config'),
            DB::raw('SUM(CASE WHEN routes THEN 1 ELSE 0 END) as routes'),
            DB::raw('SUM(CASE WHEN views THEN 1 ELSE 0 END) as views'),
            DB::raw('SUM(CASE WHEN translations THEN 1 ELSE 0 END) as translations'),
            DB::raw('SUM(CASE WHEN migrations THEN 1 ELSE 0 END) as migrations'),
            DB::raw('SUM(CASE WHEN assets THEN 1 ELSE 0 END) as assets'),
            DB::raw('SUM(CASE WHEN commands THEN 1 ELSE 0 END) as commands'),
            DB::raw('SUM(CASE WHEN facade THEN 1 ELSE 0 END) as facade'),
            DB::raw('SUM(CASE WHEN boost_skill THEN 1 ELSE 0 END) as boost_skill'),
        )->first();

        $totalPackages = $packageQuery->count();

        return Inertia::render('Stats/Index', [
            'phpVersions' => $phpVersions,
            'services' => $services,
            'starterKits' => $starterKits,
            'javascriptRuntimes' => $javascriptRuntimes,
            'authProviders' => $authProviders,
            'testingFrameworks' => $testingFrameworks,
            'databaseDrivers' => $databaseDrivers,
            'booleanOptions' => $booleanOptions,
            'totalApps' => $totalApps,
            'totalPackages' => $totalPackages,
            'total' => $totalApps + $totalPackages,
            'packagePhpVersions' => $packagePhpVersions,
            'packageFeatureOptions' => $packageFeatureOptions,
            'filters' => ['from' => $from, 'to' => $to],
        ]);
    }
}
