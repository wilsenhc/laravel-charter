<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class);

arch('controllers have no protected or private methods')
    ->expect('App\Http\Controllers')
    ->not->toHaveProtectedMethods()
    ->not->toHavePrivateMethods();

arch('Not debugging statements are left in our code.')
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump', 'ray', 'rd', 'var_dump', 'print_r']);

arch('The codebase does not reference env variables outside of config files')
    ->expect('env')
    ->not->toBeUsed();

arch('Tests have the correct suffix')
    ->expect('Tests')
    ->and('Tests\\Feature')->toHaveSuffix('Test')
    ->and('Tests\\Unit')->toHaveSuffix('Test');

arch('Action classes should be invokable')
    ->expect('App\Actions')
    ->toBeInvokable();

arch('Actions should have Action suffix')
    ->expect('App\Actions')
    ->toHaveSuffix('Action');

arch('Commands should have Command suffix.')
    ->expect('App\Console\Commands')
    ->toHaveSuffix('Command');

arch('Jobs should have Job suffix')
    ->expect('App\Jobs')
    ->toHaveSuffix('Job');

arch('Observers should have Observer suffix')
    ->expect('App\Observers')
    ->toHaveSuffix('Observer');

arch('Policies should have Policy suffix')
    ->expect('App\Policies')
    ->toHaveSuffix('Policy');

arch('Rules should have Rule suffix')
    ->expect('App\Rules')
    ->toHaveSuffix('Rule');

arch('Services should have Service suffix')
    ->expect('App\Services')
    ->toHaveSuffix('Service');

arch('Carbon\Carbon is not used, only Carbon\CarbonImmutable')
    ->expect('App')
    ->not->toUse('Carbon\Carbon')
    ->not->toUse('Illuminate\Support\Carbon');

test('Enum cases are all uppercase', function () {
    $files = File::allFiles(app_path());

    foreach ($files as $file) {
        $class = 'App'.str_replace(['/', '.php'], ['\\', ''], Str::after($file->getPathname(), app_path()));

        if (! enum_exists($class)) {
            continue;
        }

        foreach ((new ReflectionEnum($class))->getCases() as $case) {
            expect($case->getName())
                ->toMatch('/^[A-Z][A-Z0-9_]*$/', "{$class}::{$case->getName()} must be uppercase");
        }
    }
});
