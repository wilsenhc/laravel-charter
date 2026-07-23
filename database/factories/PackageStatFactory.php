<?php

namespace Database\Factories;

use App\Models\PackageStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackageStat>
 */
class PackageStatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'php_version' => '8.5',
            'config' => false,
            'routes' => false,
            'views' => false,
            'translations' => false,
            'migrations' => false,
            'assets' => false,
            'commands' => false,
            'facade' => false,
            'boost_skill' => false,
            'mcp_source' => 'web',
        ];
    }
}
