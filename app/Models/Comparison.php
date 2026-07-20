<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperComparison
 */
class Comparison extends Model
{
    protected $fillable = [
        'slug',
        'first_term_slug',
        'second_term_slug',
        'category',
        'related',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'related' => 'array',
            'translations' => 'array',
        ];
    }
}
