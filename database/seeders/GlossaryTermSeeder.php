<?php

namespace Database\Seeders;

use App\Models\GlossaryTerm;
use Illuminate\Database\Seeder;

class GlossaryTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = json_decode(
            file_get_contents(database_path('data/glossary-terms.json')) ?: '[]',
            true
        );

        foreach ($terms as $data) {
            GlossaryTerm::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category' => $data['category'],
                    'builder_params' => $data['builder_params'],
                    'related' => $data['related'],
                    'translations' => $data['translations'],
                ]
            );
        }
    }
}
