<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ApplicationStatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperApplicationStat
 */
#[Fillable([
    'php_version',
    'starter_kit',
    'custom_starter_kit',
    'javascript_runtime',
    'auth_provider',
    'testing_framework',
    'teams',
    'boost',
    'devcontainer',
    'no_node',
    'livewire_class_components',
    'database_driver',
    'mcp_source',
])]
#[Table(name: 'application_stats')]
class ApplicationStat extends Model
{
    /** @use HasFactory<ApplicationStatFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'custom_starter_kit' => 'boolean',
            'teams' => 'boolean',
            'boost' => 'boolean',
            'devcontainer' => 'boolean',
            'no_node' => 'boolean',
            'livewire_class_components' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<ApplicationService, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ApplicationService::class, 'application_stat_services', 'stat_id', 'service_id');
    }
}
