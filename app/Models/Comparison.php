<?php

namespace App\Models;

use App\Enums\Locale;
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

    /**
     * Whether this comparison has a complete translation for the given locale.
     */
    public function hasFullTranslation(string $locale): bool
    {
        $translations = $this->translations[$locale] ?? null;

        if (! is_array($translations)) {
            return false;
        }

        return collect(['page_title', 'meta_description', 'verdict'])
            ->every(fn (string $key): bool => trim((string) ($translations[$key] ?? '')) !== '')
            && collect(['overview', 'performance', 'ease_of_use', 'ecosystem', 'learning_curve'])
                ->every(function (string $key) use ($translations): bool {
                    $aspect = $translations[$key] ?? null;

                    return is_array($aspect)
                        && trim((string) ($aspect['first'] ?? '')) !== ''
                        && trim((string) ($aspect['second'] ?? '')) !== '';
                });
    }

    /**
     * @return array<string>
     */
    public function publishedLocales(): array
    {
        return collect(Locale::cases())
            ->filter(fn (Locale $locale): bool => $this->hasFullTranslation($locale->value))
            ->map(fn (Locale $locale): string => $locale->value)
            ->values()
            ->all();
    }
}
