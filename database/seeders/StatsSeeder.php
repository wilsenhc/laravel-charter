<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatsSeeder extends Seeder
{
    public function run(): void
    {
        $services = Service::pluck('id', 'name');

        $records = [];

        for ($i = 0; $i < 200; $i++) {
            $phpVersion = $this->weighted(['8.5', '8.4', '8.3'], [60, 30, 10]);
            $starterKit = $this->weighted(
                ['react', 'vue', 'livewire', 'svelte', 'none', 'custom'],
                [30, 25, 20, 10, 10, 5],
            );
            $javascriptRuntime = $this->weighted(
                ['bun', 'npm', 'pnpm', 'yarn'],
                [40, 30, 20, 10],
            );
            $authProvider = $this->weighted(
                ['laravel', 'no-authentication', 'workos'],
                [55, 30, 15],
            );
            $testingFramework = $this->weighted(['pest', 'phpunit'], [75, 25]);
            $teams = $this->randomBool(20);
            $boost = $this->randomBool(75);
            $devcontainer = $this->randomBool(15);
            $noNode = $this->randomBool(10);
            $livewireClassComponents = $starterKit === 'livewire' ? $this->randomBool(30) : false;
            $customStarterKit = $starterKit === 'custom';

            $daysAgo = fake()->numberBetween(0, 90);
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

        Stat::insert($records);

        $stats = Stat::all();

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
     * @return array<int>
     */
    private function pickServices(iterable $services): array
    {
        $mustHave = ['redis', 'mailpit'];
        $optional = array_keys($services->toArray());

        $selected = $mustHave;

        foreach ($optional as $service) {
            if (in_array($service, $mustHave, true)) {
                continue;
            }

            $popularity = match ($service) {
                'mysql', 'pgsql' => 65,
                'mariadb', 'typesense', 'meilisearch' => 30,
                'minio', 'valkey', 'mongodb' => 20,
                default => 10,
            };

            if ($this->randomBool($popularity)) {
                $selected[] = $service;
            }
        }

        return array_map(fn (string $name) => $services[$name], $selected);
    }
}
