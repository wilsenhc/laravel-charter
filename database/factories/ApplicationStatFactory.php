<?php

namespace Database\Factories;

use App\Models\ApplicationStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationStat>
 */
class ApplicationStatFactory extends Factory
{
    protected $model = ApplicationStat::class;

    public function definition(): array
    {
        return [
            'php_version' => '8.5',
            'starter_kit' => 'react',
            'custom_starter_kit' => false,
            'javascript_runtime' => 'bun',
            'auth_provider' => 'laravel',
            'testing_framework' => 'pest',
            'teams' => false,
            'boost' => true,
            'devcontainer' => false,
            'no_node' => false,
            'livewire_class_components' => false,
            'database_driver' => null,
            'mcp_source' => 'web',
        ];
    }
}
