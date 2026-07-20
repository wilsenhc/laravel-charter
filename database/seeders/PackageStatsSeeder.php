<?php

namespace Database\Seeders;

use App\Models\PackageStat;
use Illuminate\Database\Seeder;

class PackageStatsSeeder extends Seeder
{
    public function run(): void
    {
        $records = [];

        for ($i = 0; $i < 100; $i++) {
            $phpVersion = $this->weighted(['8.5', '8.4', '8.3'], [50, 35, 15]);

            $daysAgo = fake()->numberBetween(0, 180);
            $createdAt = now()->subDays($daysAgo)->subHours(fake()->numberBetween(0, 23));

            $records[] = [
                'php_version' => $phpVersion,
                'config' => $this->randomBool(50),
                'routes' => $this->randomBool(40),
                'views' => $this->randomBool(45),
                'translations' => $this->randomBool(25),
                'migrations' => $this->randomBool(60),
                'assets' => $this->randomBool(15),
                'commands' => $this->randomBool(30),
                'facade' => $this->randomBool(20),
                'boost_skill' => $this->randomBool(10),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        PackageStat::insert($records);
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
}
