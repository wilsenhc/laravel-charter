<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $guides = json_decode(
            file_get_contents(database_path('data/guides.json')) ?: '[]',
            true
        );

        foreach ($guides as $data) {
            Guide::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category' => $data['category'],
                    'builder_params' => $data['builder_params'],
                    'related' => $data['related'],
                    'app_name' => $data['app_name'],
                    'translations' => $data['translations'],
                ]
            );
        }
    }
}
