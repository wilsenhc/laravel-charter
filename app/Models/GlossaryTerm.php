<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGlossaryTerm
 */
class GlossaryTerm extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'builder_params',
        'related',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'builder_params' => 'array',
            'related' => 'array',
            'translations' => 'array',
        ];
    }
}
