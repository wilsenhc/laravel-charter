<?php

use App\Actions\BuildPackageScript;

test('generates a build script for minimal input', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
    ]);

    expect($script)
        ->toContain('laravel package my-package')
        ->toContain('php:8.5-cli')
        ->not->toContain('--config');
});

test('includes all feature flags when all features provided', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
        'features' => ['config', 'routes', 'views', 'translations', 'migrations', 'assets', 'commands', 'facade', 'boost-skill'],
    ]);

    expect($script)
        ->toContain('--config')
        ->toContain('--routes')
        ->toContain('--views')
        ->toContain('--translations')
        ->toContain('--migrations')
        ->toContain('--assets')
        ->toContain('--commands')
        ->toContain('--facade')
        ->toContain('--boost-skill');
});

test('no features are added by default', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
    ]);

    expect($script)
        ->not->toContain('--config')
        ->not->toContain('--routes')
        ->not->toContain('--views');
});

test('single feature can be included', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
        'features' => ['views'],
    ]);

    expect($script)->toContain('--views');
});

test('includes all metadata fields', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
        'author_name' => 'John Doe',
        'author_email' => 'john@example.com',
        'package_name' => 'vendor/my-package',
        'package_name_human' => 'My Package',
        'package_description' => 'A great package',
        'vendor_namespace' => 'Vendor',
        'class_name' => 'MyPackage',
    ]);

    expect($script)
        ->toContain('--author-name=\\"John Doe\\"')
        ->toContain('--author-email=\\"john@example.com\\"')
        ->toContain('--package-name=\\"vendor/my-package\\"')
        ->toContain('--package-name-human=\\"My Package\\"')
        ->toContain('--package-description=\\"A great package\\"')
        ->toContain('--vendor-namespace=\\"Vendor\\"')
        ->toContain('--class-name=\\"MyPackage\\"');
});

test('metadata fields are omitted by default', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
    ]);

    expect($script)
        ->not->toContain('--author-name')
        ->not->toContain('--author-email')
        ->not->toContain('--package-name')
        ->not->toContain('--vendor-namespace');
});

test('php versions produce correct cli image', function (string $php) {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
        'php' => $php,
    ]);

    expect($script)->toContain("php:{$php}-cli");
})->with(['8.5', '8.4', '8.3']);

test('all options work together', function () {
    $script = app(BuildPackageScript::class)->handle([
        'name' => 'my-package',
        'features' => ['config', 'routes', 'views'],
        'author_name' => 'John',
        'php' => '8.4',
    ]);

    expect($script)
        ->toContain('--config')
        ->toContain('--routes')
        ->toContain('--views')
        ->toContain('--author-name=\\"John\\"')
        ->toContain('php:8.4-cli');
});
