<?php

namespace App\Models;

use Database\Factories\StatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stat extends Model
{
    /** @use HasFactory<StatFactory> */
    use HasFactory;

    protected $fillable = [
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
    ];

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
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'stat_services');
    }
}
