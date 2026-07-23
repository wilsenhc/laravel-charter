<?php

use App\Actions\BuildApplicationScript;

test('generates a build script for minimal input', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['pgsql', 'redis'],
    ]);

    expect($script)
        ->toContain('laravel new my-app')
        ->toContain('sail:install --with=pgsql,redis')
        ->toContain('--php=8.5')
        ->toContain('--no-boost');
});

test('includes all flags when all options provided', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['pgsql', 'redis'],
        'frontend' => 'vue',
        'php' => '8.5',
        'testing' => 'pest',
        'javascript' => 'bun',
        'boost' => true,
        'teams' => true,
        'database' => 'pgsql',
        'devcontainer' => true,
        'no-node' => true,
    ]);

    expect($script)
        ->toContain('--vue')
        ->toContain('--php=8.5')
        ->toContain('--pest')
        ->toContain('--bun')
        ->toContain('--boost')
        ->toContain('--teams')
        ->toContain('--database=pgsql')
        ->toContain('--devcontainer')
        ->toContain('--no-node');
});

test('defaults to no-boost', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
    ]);

    expect($script)
        ->toContain('--no-boost')
        ->not->toContain(' --boost');
});

test('custom starter kit includes node volume', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => 'custom',
        'using' => 'https://example.com/kit',
    ]);

    expect($script)
        ->toContain('docker volume create node-binaries')
        ->toContain('node:24-slim')
        ->toContain('-v node-binaries:/usr/local/node:ro')
        ->toContain('export PATH=/usr/local/node/bin')
        ->toContain('docker volume rm node-binaries');
});

test('standard starter kit does not include node volume', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => 'vue',
    ]);

    expect($script)
        ->not->toContain('node-binaries')
        ->not->toContain('node:24-slim');
});

test('default build does not include node volume', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
    ]);

    expect($script)
        ->not->toContain('node-binaries')
        ->not->toContain('node:24-slim');
});

test('livewire with class components adds both flags', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => 'livewire',
        'livewire-class-components' => true,
    ]);

    expect($script)->toContain('--livewire --livewire-class-components');
});

test('livewire without class components does not add modifier', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => 'livewire',
    ]);

    expect($script)
        ->toContain('--livewire')
        ->not->toContain('--livewire-class-components');
});

test('no-node flag only appears when specified', function () {
    $with = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'no-node' => true,
    ]);

    $without = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
    ]);

    expect($with)->toContain('--no-node');
    expect($without)->not->toContain('--no-node');
});

test('database flag is omitted when none', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'database' => 'none',
    ]);

    expect($script)->not->toContain('--database=');
});

test('database flag is omitted by default', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
    ]);

    expect($script)->not->toContain('--database=');
});

test('database flag is added when specified', function (string $driver) {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'database' => $driver,
    ]);

    expect($script)->toContain("--database={$driver}");
})->with(['mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv']);

test('teams flag is added when true', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'teams' => true,
    ]);

    expect($script)->toContain('--teams');
});

test('teams flag is omitted by default', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
    ]);

    expect($script)->not->toContain('--teams');
});

test('auth flags are added correctly', function (string $auth, string $flag) {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'auth' => $auth,
    ]);

    expect($script)->toContain("--{$flag}");
})->with([
    ['no-authentication', 'no-authentication'],
    ['workos', 'workos'],
]);

test('testing flags are added correctly', function (string $testing) {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'testing' => $testing,
    ]);

    expect($script)->toContain("--{$testing}");
})->with(['pest', 'phpunit']);

test('devcontainer flag is added when specified', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'devcontainer' => true,
    ]);

    expect($script)->toContain('--devcontainer');
});

test('none as only service uses --with=none', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['none'],
    ]);

    expect($script)->toContain('--with=none');
});

test('frontend=none produces no frontend flag', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => 'none',
    ]);

    expect($script)
        ->not->toContain('--none')
        ->not->toContain('--custom');
});

test('custom starter kit uses --using flag', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => 'custom',
        'using' => 'https://example.com/starter-kit',
    ]);

    expect($script)->toContain('--using="https://example.com/starter-kit"');
});

test('starter kits produce correct flags', function (string $kit, string $flag) {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'frontend' => $kit,
    ]);

    expect($script)->toContain("--{$flag}");
})->with([
    ['react', 'react'],
    ['vue', 'vue'],
    ['svelte', 'svelte'],
    ['livewire', 'livewire'],
]);

test('javascript runtime sets correct install and dev commands', function (string $runtime, string $installCmd, string $devCmd) {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'javascript' => $runtime,
    ]);

    expect($script)
        ->toContain("sail {$installCmd}")
        ->toContain("sail {$devCmd}");
})->with([
    ['npm', 'npm install', 'npm run dev'],
    ['pnpm', 'pnpm install', 'pnpm run dev'],
    ['bun', 'bun install', 'bun run dev'],
    ['yarn', 'yarn install', 'yarn dev'],
]);

test('javascript runtime defaults to npm when not set', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
    ]);

    expect($script)
        ->toContain('sail npm install')
        ->toContain('sail npm run dev');
});

test('php versions produce correct flag', function (string $php) {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['redis'],
        'php' => $php,
    ]);

    expect($script)->toContain("--php={$php}");
})->with(['8.5', '8.4', '8.3']);

test('all options work together', function () {
    $script = app(BuildApplicationScript::class)->handle([
        'name' => 'my-app',
        'services' => ['pgsql', 'redis'],
        'frontend' => 'svelte',
        'javascript' => 'bun',
        'boost' => true,
        'teams' => true,
        'auth' => 'no-authentication',
        'testing' => 'pest',
    ]);

    expect($script)
        ->toContain('--svelte')
        ->toContain('--bun')
        ->toContain('--boost')
        ->toContain('--teams')
        ->toContain('--no-authentication')
        ->toContain('--pest')
        ->not->toContain('--no-boost');
});
