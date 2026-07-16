<?php

namespace App\Http\Requests;

use App\Enums\BuildOptions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BuildPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $features = $this->query('features', '');

        $this->merge([
            'features' => $features === '' ? [] : explode(',', $features),
            'php' => $this->query('php', '8.5'),
        ]);
    }

    /**
     * @return array<string, list<string|Rule>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'alpha_dash'],
            'php' => ['string', Rule::in(BuildOptions::AvailablePhpVersions->values())],
            'features' => ['array'],
            'features.*' => ['string', Rule::in(BuildOptions::AvailablePackageFeatures->values())],
            'author_name' => ['nullable', 'string', 'max:255'],
            'author_email' => ['nullable', 'email', 'max:255'],
            'package_name' => ['nullable', 'string', 'max:255'],
            'package_name_human' => ['nullable', 'string', 'max:255'],
            'package_description' => ['nullable', 'string', 'max:500'],
            'vendor_namespace' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'class_name' => ['nullable', 'string', 'max:255', 'alpha_dash'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $errors = $validator->errors()->toArray();
        $hasError = static fn (string $field): bool => collect($errors)
            ->keys()
            ->contains(fn (string $key): bool => $key === $field || str_starts_with($key, "{$field}."));

        $messages = [];

        if ($hasError('name')) {
            $messages[] = 'Invalid package name. Please provide a valid name (alpha_dash characters only).';
        }

        if ($hasError('php')) {
            $messages[] = 'Invalid PHP version. Please provide one supported version ('.implode(', ', BuildOptions::AvailablePhpVersions->values()).').';
        }

        if ($hasError('features')) {
            $messages[] = 'Invalid feature. Please provide one or more of the supported features ('.implode(', ', BuildOptions::AvailablePackageFeatures->values()).').';
        }

        if ($hasError('author_email')) {
            $messages[] = 'Invalid author email. Please provide a valid email address.';
        }

        $name = (string) $this->query('name', '');
        $safeName = preg_match('/^[a-zA-Z0-9_-]+$/', $name) ? $name : '';

        $response = response(
            str_replace(
                ['{{ name }}', '{{ errors }}'],
                [$safeName, $this->formatErrors($messages)],
                (string) file_get_contents(resource_path('stubs/error.sh')),
            ),
            400,
            ['Content-Type' => 'text/plain'],
        );

        throw new ValidationException($validator, $response);
    }

    /**
     * @param  array<string>  $messages
     */
    private function formatErrors(array $messages): string
    {
        return collect($messages)
            ->map(fn (string $message) => 'echo "'.str_replace('"', '\\"', $message).'"')
            ->implode("\n");
    }
}
