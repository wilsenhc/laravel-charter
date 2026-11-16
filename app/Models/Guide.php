<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Locale;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGuide
 */
#[Fillable([
    'slug',
    'category',
    'builder_params',
    'related',
    'app_name',
    'translations',
])]
class Guide extends Model
{
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
            && $this->paragraphsAreComplete($translations['notes'] ?? null)
            && $this->paragraphsAreComplete($translations['steps'] ?? null);
    }

    private function paragraphsAreComplete(mixed $paragraphs): bool
    {
        if (! is_array($paragraphs) || $paragraphs === []) {
            return false;
        }

        return array_all($paragraphs, fn ($paragraph) => is_string($paragraph) && trim($paragraph) !== '');
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
