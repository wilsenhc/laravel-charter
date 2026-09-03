<?php

namespace App\Providers;

use App\Enums\BuildOptions;
use App\Enums\Locale;
use App\Models\Comparison;
use App\Models\GlossaryTerm;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SchaeferSoft\LaravelLlmsTxt\Entry;
use SchaeferSoft\LaravelLlmsTxt\LlmsTxt;
use SchaeferSoft\LaravelLlmsTxt\Section;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureLlmsTxt();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Define the /llms.txt document served to LLM agents.
     */
    protected function configureLlmsTxt(): void
    {
        LlmsTxt::configure(function (LlmsTxt $llms): void {
            $locale = Locale::default()->value;

            $llms
                ->title('Charter for Laravel')
                ->description('Charter simplifies creating new Laravel apps and packages: pick your services and options visually, then copy a single CLI command that scaffolds your project with Laravel Sail from the start.')
                ->section('Build', function (Section $section) use ($locale): void {
                    $section
                        ->entry('Application builder', route('build.application.index', ['locale' => $locale]), 'Visually configure a new Laravel Sail application and get a one-line install command.')
                        ->entry('Package builder', route('build.package.index', ['locale' => $locale]), 'Visually configure a new Laravel package and get a one-line install command.')
                        ->entry('Build statistics', route('stats.index', ['locale' => $locale]), 'Aggregate statistics of the apps and packages scaffolded through Charter.');
                })
                ->section('Glossary', function (Section $section) use ($locale): void {
                    $section
                        ->entry('Glossary index', route('glossary.index', ['locale' => $locale]), 'Laravel Sail and ecosystem terms grouped by category.')
                        ->entries(GlossaryTerm::all(), fn (GlossaryTerm $term): Entry => Entry::create(
                            $term->translations[$locale]['title'],
                            route('glossary.show', ['locale' => $locale, 'term' => $term->slug]),
                            $term->translations[$locale]['summary'],
                        ));
                })
                ->section('Comparisons', function (Section $section) use ($locale): void {
                    $section
                        ->entries(Comparison::all(), fn (Comparison $comparison): Entry => Entry::create(
                            $comparison->translations[$locale]['page_title'],
                            route('comparison.show', ['locale' => $locale, 'comparison' => $comparison->slug]),
                            $comparison->translations[$locale]['meta_description'],
                        ));
                })
                ->section('For AI agents', function (Section $section) use ($locale): void {
                    $section
                        ->entry('Charter MCP server', route('mcp.index', ['locale' => $locale]), sprintf('MCP endpoint at %s exposing the build-application and build-package tools, which return the same bash scripts with full parameter validation. Client setup examples for Claude, Cursor, Codex, and opencode.', url('/mcp/charter')))
                        ->entry('Application build endpoint', route('build.application.show'), $this->applicationBuildEndpointDescription())
                        ->entry('Package build endpoint', route('build.package.show'), $this->packageBuildEndpointDescription());
                })
                ->section('Optional', function (Section $section) use ($locale): void {
                    $section
                        ->entry('Privacy policy', route('privacy', ['locale' => $locale]))
                        ->entry('Terms of service', route('terms', ['locale' => $locale]));
                });
        });
    }

    /**
     * Enumerate the application build endpoint parameters from BuildOptions
     * so the list cannot drift from the validation rules.
     */
    protected function applicationBuildEndpointDescription(): string
    {
        return sprintf(
            "Returns a text/plain bash script. Example: curl -fsSL '%s' | bash. Parameters: name (required, alpha_dash); services[] (%s); frontend (%s, default none); auth (%s, default laravel); testing (%s, default pest); php (%s, default 8.5); database (%s, default none); javascript (%s); using (URL of a custom starter kit); boolean flags: teams, no-node, livewire-class-components, boost, devcontainer.",
            route('build.application.show', ['name' => 'blog', 'services' => ['mysql', 'redis']]),
            implode(', ', BuildOptions::AvailableServices->values()),
            implode(', ', BuildOptions::AvailableStarterKits->values()),
            implode(', ', BuildOptions::AvailableAuthProviders->values()),
            implode(', ', BuildOptions::AvailableTestingFrameworks->values()),
            implode(', ', BuildOptions::AvailablePhpVersions->values()),
            implode(', ', BuildOptions::AvailableDatabaseDrivers->values()),
            implode(', ', BuildOptions::AvailableJavascriptRuntimes->values()),
        );
    }

    /**
     * Enumerate the package build endpoint parameters from BuildOptions so
     * the list cannot drift from the validation rules.
     */
    protected function packageBuildEndpointDescription(): string
    {
        return sprintf(
            "Returns a text/plain bash script. Example: curl -fsSL '%s' | bash. Parameters: name (required, alpha_dash); features (comma-separated: %s); php (%s, default 8.5); package_name (vendor/package format); package_name_human; package_description; author_name; author_email; vendor_namespace (alpha_dash); class_name (alpha_dash).",
            route('build.package.show', ['name' => 'blog-package', 'features' => 'config,routes']),
            implode(', ', BuildOptions::AvailablePackageFeatures->values()),
            implode(', ', BuildOptions::AvailablePhpVersions->values()),
        );
    }
}
