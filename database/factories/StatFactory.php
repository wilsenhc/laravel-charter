<?php

namespace Database\Factories;

use App\Models\Stat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stat>
 */
class StatFactory extends Factory
{
    protected $model = Stat::class;

    public function definition(): array
    {
        return [
            'php_version' => fake()->randomElement(['8.5', '8.4', '8.3']),
            'starter_kit' => fake()->randomElement(['none', 'livewire', 'vue', 'react', 'svelte', 'custom']),
            'custom_starter_kit' => false,
            'javascript_runtime' => fake()->randomElement(['npm', 'pnpm', 'bun', 'yarn']),
            'auth_provider' => fake()->randomElement(['no-authentication', 'laravel', 'workos']),
            'testing_framework' => fake()->randomElement(['pest', 'phpunit']),
            'teams' => fake()->boolean(),
            'boost' => fake()->boolean(),
            'devcontainer' => fake()->boolean(),
            'no_node' => fake()->boolean(),
            'livewire_class_components' => fake()->boolean(),
        ];
    }
}
