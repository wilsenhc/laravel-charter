<?php

namespace App\Actions;

use Illuminate\Support\Facades\Blade;

class BuildPackageScript
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): string
    {
        $name = $data['name'];
        $php = $data['php'] ?? '8.5';
        $features = $data['features'] ?? [];

        $featureFlags = array_map(fn (string $feature) => "--{$feature}", $features);

        $metadataFlags = [];

        if ($authorName = $data['author_name'] ?? null) {
            $metadataFlags[] = '--author-name=\\"'.$authorName.'\\"';
        }

        if ($authorEmail = $data['author_email'] ?? null) {
            $metadataFlags[] = '--author-email=\\"'.$authorEmail.'\\"';
        }

        if ($packageName = $data['package_name'] ?? null) {
            $metadataFlags[] = '--package-name=\\"'.$packageName.'\\"';
        }

        if ($packageNameHuman = $data['package_name_human'] ?? null) {
            $metadataFlags[] = '--package-name-human=\\"'.$packageNameHuman.'\\"';
        }

        if ($packageDescription = $data['package_description'] ?? null) {
            $metadataFlags[] = '--package-description=\\"'.$packageDescription.'\\"';
        }

        if ($vendorNamespace = $data['vendor_namespace'] ?? null) {
            $metadataFlags[] = '--vendor-namespace=\\"'.$vendorNamespace.'\\"';
        }

        if ($className = $data['class_name'] ?? null) {
            $metadataFlags[] = '--class-name=\\"'.$className.'\\"';
        }

        $options = implode(' ', [
            ...$featureFlags,
            ...$metadataFlags,
        ]);

        return Blade::render(
            (string) file_get_contents(resource_path('stubs/build-package.sh')),
            [
                'name' => $name,
                'options' => $options,
                'php' => $php,
            ],
        );
    }
}
