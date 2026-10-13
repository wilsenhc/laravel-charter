<?php

namespace App\Models;

use App\Enums\Locale;
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

    /**
     * Whether this term has a complete translation for the given locale, so
     * the page never renders with empty titles or definitions.
     */
    public function hasFullTranslation(string $locale): bool
    {
        $translations = $this->translations[$locale] ?? null;

        if (! is_array($translations)) {
            return false;
        }

        return collect(['title', 'question', 'summary', 'definition', 'sail_integration'])
            ->every(fn (string $key): bool => trim((string) ($translations[$key] ?? '')) !== '');
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
