<?php

namespace App\Enums;

enum Locale: string
{
    case English = 'en';
    case Spanish = 'es';

    public function label(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Spanish => 'Español',
        };
    }

    public static function default(): self
    {
        return self::English;
    }

    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<array{code: string, label: string}>
     */
    public static function supported(): array
    {
        return array_map(fn (self $locale) => [
            'code' => $locale->value,
            'label' => $locale->label(),
        ], self::cases());
    }
}
