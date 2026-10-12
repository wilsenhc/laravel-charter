<?php

use App\Models\Comparison;
use App\Models\GlossaryTerm;
use Illuminate\Http\Response;

use function Pest\Laravel\get;

function glossaryTranslations(string $locale): array
{
    if ($locale === 'en') {
        return [
            'title' => 'English term',
            'question' => 'What is the English term?',
            'summary' => 'English summary.',
            'definition' => 'English definition.',
            'sail_integration' => 'English sail integration.',
        ];
    }

    return [
        'title' => 'Término español',
        'question' => '¿Qué es el término español?',
        'summary' => 'Resumen en español.',
        'definition' => 'Definición en español.',
        'sail_integration' => 'Integración en español.',
    ];
}

it('returns 404 for glossary terms without a full translation in the requested locale', function () {
    GlossaryTerm::create([
        'slug' => 'untranslated-term',
        'category' => 'Service',
        'builder_params' => [],
        'related' => [],
        'translations' => [
            'en' => glossaryTranslations('en'),
            'es' => [
                'title' => '',
                'question' => '',
                'summary' => '',
                'definition' => '',
                'sail_integration' => '',
            ],
        ],
    ]);

    get(route('glossary.show', ['locale' => 'es', 'term' => 'untranslated-term']))
        ->assertStatus(Response::HTTP_NOT_FOUND);

    get(route('glossary.show', ['locale' => 'en', 'term' => 'untranslated-term']))
        ->assertSuccessful();
});

it('excludes untranslated glossary terms from the localized index and sitemap', function () {
    GlossaryTerm::create([
        'slug' => 'untranslated-term',
        'category' => 'Service',
        'builder_params' => [],
        'related' => [],
        'translations' => [
            'en' => glossaryTranslations('en'),
            'es' => [
                'title' => '',
                'question' => '',
                'summary' => '',
                'definition' => '',
                'sail_integration' => '',
            ],
        ],
    ]);

    $esIndex = get(route('glossary.index', ['locale' => 'es']))
        ->assertSuccessful()
        ->getContent();

    expect($esIndex)->not->toContain('untranslated-term');

    $sitemap = get(route('sitemap'))
        ->assertSuccessful()
        ->getContent();

    expect($sitemap)
        ->toContain('/en/glossary/untranslated-term')
        ->not->toContain('/es/glossary/untranslated-term');
});

it('returns 404 for comparisons without a full translation in the requested locale', function () {
    Comparison::create([
        'slug' => 'untranslated-comparison',
        'first_term_slug' => 'mysql',
        'second_term_slug' => 'mariadb',
        'category' => 'Database',
        'related' => [],
        'translations' => [
            'en' => [
                'page_title' => 'English comparison',
                'meta_description' => 'English description.',
                'overview' => ['first' => 'a', 'second' => 'b'],
                'performance' => ['first' => 'a', 'second' => 'b'],
                'ease_of_use' => ['first' => 'a', 'second' => 'b'],
                'ecosystem' => ['first' => 'a', 'second' => 'b'],
                'learning_curve' => ['first' => 'a', 'second' => 'b'],
                'verdict' => 'English verdict.',
            ],
            'es' => [
                'page_title' => '',
                'meta_description' => '',
                'overview' => ['first' => '', 'second' => ''],
                'performance' => ['first' => '', 'second' => ''],
                'ease_of_use' => ['first' => '', 'second' => ''],
                'ecosystem' => ['first' => '', 'second' => ''],
                'learning_curve' => ['first' => '', 'second' => ''],
                'verdict' => '',
            ],
        ],
    ]);

    get(route('comparison.show', ['locale' => 'es', 'comparison' => 'untranslated-comparison']))
        ->assertStatus(Response::HTTP_NOT_FOUND);

    get(route('comparison.show', ['locale' => 'en', 'comparison' => 'untranslated-comparison']))
        ->assertSuccessful();
});
