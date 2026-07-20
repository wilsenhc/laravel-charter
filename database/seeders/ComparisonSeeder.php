<?php

namespace Database\Seeders;

use App\Models\Comparison;
use Illuminate\Database\Seeder;

class ComparisonSeeder extends Seeder
{
    public function run(): void
    {
        $comparisons = json_decode(
            file_get_contents(database_path('data/comparisons.json')) ?: '[]',
            true
        );

        foreach ($comparisons as $data) {
            Comparison::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'first_term_slug' => $data['first_term_slug'],
                    'second_term_slug' => $data['second_term_slug'],
                    'category' => $data['category'],
                    'related' => $data['related'],
                    'translations' => $data['translations'],
                ]
            );
        }
    }
}
