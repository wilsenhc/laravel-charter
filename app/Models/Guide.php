<?php

namespace App\Models;

use App\Enums\Locale;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGuide
 */
class Guide extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'builder_params',
        'related',
        'app_name',
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
     * Whether this guide has a complete translation for the given locale,
     * so the page never renders with empty headings or descriptions.
     */
    public function hasFullTranslation(string $locale): bool
    {
        $translations = $this->translations[$locale] ?? null;

        if (! is_array($translations)) {
            return false;
        }

        return trim((string) ($translations['title'] ?? '')) !== ''
            && trim((string) ($translations['meta_description'] ?? '')) !== ''
            && trim((string) ($translations['heading'] ?? '')) !== ''
            && trim((string) ($translations['problem'] ?? '')) !== ''
            && self::paragraphsAreComplete($translations['notes'] ?? null)
            && self::paragraphsAreComplete($translations['steps'] ?? null);
    }

    private static function paragraphsAreComplete(mixed $paragraphs): bool
    {
        if (! is_array($paragraphs) || $paragraphs === []) {
            return false;
        }

        foreach ($paragraphs as $paragraph) {
            if (! is_string($paragraph) || trim($paragraph) === '') {
                return false;
            }
        }

        return true;
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
