<?php

namespace App\Http\Requests;

use App\Enums\BuildOptions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ConditionalRules;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationException;

class BuildShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $services = $this->query('services', 'none');

        $this->merge([
            'services' => $services === 'none' ? ['none'] : explode(',', $services),
            'frontend' => $this->query('frontend', 'none'),
            'testing' => $this->query('testing', 'pest'),
            'php' => $this->query('php', '8.5'),
            'teams' => $this->has('teams'),
            'no-node' => $this->has('no-node'),
        ]);
    }

    /**
     * @return array<string, list<string|In>|ConditionalRules>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'alpha_dash'],
            'services' => ['required', 'array'],
            'services.*' => ['string', Rule::in([...BuildOptions::AvailableServices->values(), 'none'])],
            'frontend' => ['string', Rule::in(BuildOptions::AvailableStarterKits->values())],
            'auth' => ['nullable', 'string', Rule::in(BuildOptions::AvailableAuthProviders->values())],
            'testing' => ['string', Rule::in(BuildOptions::AvailableTestingFrameworks->values())],
            'javascript' => ['nullable', 'string', Rule::in(BuildOptions::AvailableJavascriptRuntimes->values())],
            'php' => ['string', Rule::in(BuildOptions::AvailablePhpVersions->values())],
            'using' => Rule::when(
                $this->query('frontend', 'none') === 'custom',
                ['required', 'string', 'url'],
                ['nullable', 'string', 'url'],
            ),
            'teams' => ['boolean'],
            'no-node' => ['boolean'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $services = $this->array('services');

                if (in_array('none', $services, true) && count($services) > 1) {
                    $validator->errors()->add(
                        'services',
                        'Cannot use "none" with other services.',
                    );
                }
            },
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
            $messages[] = 'Invalid application name. Please provide a valid name (alpha_dash characters only).';
        }

        if ($hasError('services')) {
            $messages[] = 'Invalid service name. Please provide one or more of the supported services ('.implode(', ', BuildOptions::AvailableServices->values()).') or "none".';
        }

        if ($hasError('frontend')) {
            $messages[] = 'Invalid starter kit. Please provide one supported starter kit ('.implode(', ', BuildOptions::AvailableStarterKits->values()).') or leave it empty.';
        }

        if ($hasError('auth')) {
            $messages[] = 'Invalid authentication provider. Please provide one supported provider ('.implode(', ', BuildOptions::AvailableAuthProviders->values()).') or leave it empty.';
        }

        if ($hasError('testing')) {
            $messages[] = 'Invalid testing framework. Please provide one supported testing framework ('.implode(', ', BuildOptions::AvailableTestingFrameworks->values()).') or leave it empty (it will use pest).';
        }

        if ($hasError('javascript')) {
            $messages[] = 'Invalid JavaScript runtime. Please provide one supported runtime ('.implode(', ', BuildOptions::AvailableJavascriptRuntimes->values()).') or leave it empty.';
        }

        if ($hasError('php')) {
            $messages[] = 'Invalid PHP version. Please provide one supported version ('.implode(', ', BuildOptions::AvailablePhpVersions->values()).').';
        }

        if ($hasError('using')) {
            if ($this->query('frontend', 'none') === 'custom') {
                $messages[] = 'Invalid custom starter kit URL. A valid URL is required when using a custom starter kit.';
            } else {
                $messages[] = 'Invalid custom starter kit URL. Please provide a valid URL.';
            }
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
