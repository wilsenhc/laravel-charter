<?php

namespace App\Http\Controllers;

use App\Models\Stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StatsController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $query = Stat::query();

        $from = $request->query('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->query('to');

        $query->whereDate('stats.created_at', '>=', $from);

        if ($to) {
            $query->whereDate('stats.created_at', '<=', $to);
        }

        $phpVersions = (clone $query)
            ->selectRaw('php_version, count(*) as count')
            ->groupBy('php_version')
            ->orderByDesc('count')
            ->pluck('count', 'php_version');

        $services = (clone $query)
            ->join('stat_services', 'stats.id', '=', 'stat_services.stat_id')
            ->join('services', 'stat_services.service_id', '=', 'services.id')
            ->selectRaw('services.name, count(*) as count')
            ->groupBy('services.name')
            ->orderByDesc('count')
            ->pluck('count', 'name');

        $starterKits = (clone $query)
            ->selectRaw('starter_kit, count(*) as count')
            ->groupBy('starter_kit')
            ->orderByDesc('count')
            ->pluck('count', 'starter_kit');

        $javascriptRuntimes = (clone $query)
            ->whereNotNull('javascript_runtime')
            ->selectRaw('javascript_runtime, count(*) as count')
            ->groupBy('javascript_runtime')
            ->orderByDesc('count')
            ->pluck('count', 'javascript_runtime');

        $authProviders = (clone $query)
            ->whereNotNull('auth_provider')
            ->selectRaw('auth_provider, count(*) as count')
            ->groupBy('auth_provider')
            ->orderByDesc('count')
            ->pluck('count', 'auth_provider');

        $testingFrameworks = (clone $query)
            ->selectRaw('testing_framework, count(*) as count')
            ->groupBy('testing_framework')
            ->orderByDesc('count')
            ->pluck('count', 'testing_framework');

        $booleanOptions = (clone $query)->toBase()->select(
            DB::raw('SUM(teams) as teams'),
            DB::raw('SUM(boost) as boost'),
            DB::raw('SUM(devcontainer) as devcontainer'),
            DB::raw('SUM(no_node) as no_node'),
            DB::raw('SUM(livewire_class_components) as livewire_class_components'),
            DB::raw('SUM(custom_starter_kit) as custom_starter_kit'),
        )->first();

        return Inertia::render('Stats/Index', [
            'phpVersions' => $phpVersions,
            'services' => $services,
            'starterKits' => $starterKits,
            'javascriptRuntimes' => $javascriptRuntimes,
            'authProviders' => $authProviders,
            'testingFrameworks' => $testingFrameworks,
            'booleanOptions' => $booleanOptions,
            'total' => $query->count(),
            'filters' => ['from' => $from, 'to' => $to],
        ]);
    }
}
