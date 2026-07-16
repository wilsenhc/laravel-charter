<?php

namespace Database\Seeders;

use App\Models\ApplicationService;
use App\Models\ApplicationStat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ApplicationStatsSeeder extends Seeder
{
    public function run(): void
    {
        $services = ApplicationService::pluck('id', 'name');

        $records = [];

        for ($i = 0; $i < 300; $i++) {
            $phpVersion = $this->weighted(['8.5', '8.4', '8.3'], [50, 35, 15]);
            $starterKit = $this->weighted(
                ['react', 'vue', 'livewire', 'svelte', 'none', 'custom'],
                [25, 20, 20, 15, 15, 5],
            );
            $javascriptRuntime = $this->weighted(
                ['bun', 'npm', 'pnpm', 'yarn'],
                [35, 30, 20, 15],
            );
            $authProvider = $this->weighted(
                ['laravel', 'no-authentication', 'workos'],
                [50, 30, 20],
            );
            $testingFramework = $this->weighted(['pest', 'phpunit'], [70, 30]);
            $teams = $this->randomBool(35);
            $boost = $this->randomBool(60);
            $devcontainer = $this->randomBool(30);
            $noNode = $this->randomBool(20);
            $livewireClassComponents = $starterKit === 'livewire' ? $this->randomBool(40) : false;
            $customStarterKit = $starterKit === 'custom';

            $daysAgo = fake()->numberBetween(0, 180);
            $createdAt = now()->subDays($daysAgo)->subHours(fake()->numberBetween(0, 23));

            $records[] = [
                'php_version' => $phpVersion,
                'starter_kit' => $starterKit,
                'custom_starter_kit' => $customStarterKit,
                'javascript_runtime' => $customStarterKit ? null : $javascriptRuntime,
                'auth_provider' => $starterKit === 'none' ? null : $authProvider,
                'testing_framework' => $testingFramework,
                'teams' => $teams,
                'boost' => $boost,
                'devcontainer' => $devcontainer,
                'no_node' => $noNode,
                'livewire_class_components' => $livewireClassComponents,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        ApplicationStat::insert($records);

        $stats = ApplicationStat::all();

        foreach ($stats as $stat) {
            $selectedServices = $this->pickServices($services);

            if (! empty($selectedServices)) {
                $stat->services()->sync($selectedServices);
            }
        }
    }

    /**
     * @param  array<string>  $options
     * @param  array<int>  $weights
     */
    private function weighted(array $options, array $weights): string
    {
        $total = array_sum($weights);
        $random = fake()->randomDigit() / 10 * $total;
        $cumulative = 0;

        foreach (array_combine($options, $weights) as $option => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $option;
            }
        }

        return $options[0];
    }

    private function randomBool(int $truePercentage): bool
    {
        return fake()->numberBetween(1, 100) <= $truePercentage;
    }

    /**
     * @param  Collection<string, int>  $services
     * @return array<int>
     */
    private function pickServices(Collection $services): array
    {
        $selected = [];

        foreach ($services as $name => $id) {
            $popularity = match ($name) {
                'redis', 'mailpit' => 80,
                'mysql', 'pgsql' => 55,
                'mariadb', 'typesense', 'meilisearch' => 25,
                'minio', 'valkey', 'mongodb' => 20,
                default => 10,
            };

            if ($this->randomBool($popularity)) {
                $selected[] = $name;
            }
        }

        return $services->only($selected)->values()->toArray();
    }
}
