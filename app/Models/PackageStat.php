<?php

namespace App\Models;

use Database\Factories\PackageStatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPackageStat
 */
class PackageStat extends Model
{
    /** @use HasFactory<PackageStatFactory> */
    use HasFactory;

    protected $fillable = [
        'php_version',
        'config',
        'routes',
        'views',
        'translations',
        'migrations',
        'assets',
        'commands',
        'facade',
        'boost_skill',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'boolean',
            'routes' => 'boolean',
            'views' => 'boolean',
            'translations' => 'boolean',
            'migrations' => 'boolean',
            'assets' => 'boolean',
            'commands' => 'boolean',
            'facade' => 'boolean',
            'boost_skill' => 'boolean',
        ];
    }
}
