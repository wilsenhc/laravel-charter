# Contributing to Charter for Laravel

Thanks for taking the time to help. This guide covers everything you need to get Charter running locally and open a pull request that gets reviewed quickly.

- [Find something to work on](#find-something-to-work-on)
- [Set up Charter locally](#set-up-charter-locally)
- [Understand the project](#understand-the-project)
- [Run the checks](#run-the-checks)
- [Open a pull request](#open-a-pull-request)
- [Areas that always need help](#areas-that-always-need-help)
- [Code style](#code-style)
- [Reporting security issues](#reporting-security-issues)

## Find something to work on

- Issues labelled [`good first issue`](https://github.com/wilsenhc/laravel-charter/labels) are scoped to be small and self-contained.
- [`help wanted`](https://github.com/wilsenhc/laravel-charter/labels) marks work where input is welcome but the shape is still flexible.
- Anything not tracked yet is fine too — open an issue first so we can agree on scope before you spend a weekend on it.

If you are unsure where to start, comment on an issue before you begin. A short "I'd like to take this" is enough to claim it.

## Set up Charter locally

### Requirements

- **PHP 8.5** — pinned in `.php-version`, and what CI runs
- [Composer 2](https://getcomposer.org)
- [Bun](https://bun.sh) — this project uses Bun, not npm
- A database. Tests run against the `laravel_charter_testing` database configured in `.env.testing`.

### Install

```bash
git clone https://github.com/wilsenhc/laravel-charter.git
cd laravel-charter

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite   # .env.example defaults to SQLite; Laravel will not create it for you
php artisan migrate

bun install
bun run build
```

`composer setup` performs the same steps in one command, except the `touch`.

### Tests need their own database

`.env.testing` is git-ignored and holds the database connection the suite runs against. It is loaded automatically when `APP_ENV=testing`. Point it at a database you are happy for the suite to reset:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_charter_testing
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Run the suite against SQLite instead by setting `DB_CONNECTION=sqlite` and creating the file:

```bash
touch database/testing.sqlite
```

### Prefer PostgreSQL for the app itself?

Point `.env` at a PostgreSQL instance to match CI:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=charter
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

The repository also ships a Sail-based stack for running the app itself in containers — see [`compose.yaml`](compose.yaml) and [`docker/`](docker). It is a **development** environment, not a production deployment recipe.

### Run it

```bash
composer run dev
```

This starts the PHP server, a queue listener, log tailing, and the Vite dev server together. The app is then at `http://localhost:8000`.

## Understand the project

Charter is a Laravel 13 app that renders two builders:

| Path | What lives there |
| --- | --- |
| `app/Enums/BuildOptions.php` | The single source of truth for every selectable option. Adding an option starts here. |
| `app/Actions/BuildApplicationScriptAction.php` | Turns selections into installer flags for applications. |
| `app/Actions/BuildPackageScriptAction.php` | Same, for packages. |
| `resources/stubs/build-application.sh` | The shell script template users receive. |
| `resources/stubs/build-package.sh` | The package script template. |
| `app/Mcp/` | The MCP server, its tools, and the `build-application` / `build-package` tools. |
| `resources/js/pages/Build/` | The Vue pages for both builders. |
| `lang/en.json`, `lang/es.json` | Every user-facing string. |

A change to one path usually needs a matching change in the others — for example, adding a service means updating `BuildOptions`, both stub templates, and both language files.

## Run the checks

Run the same gate CI runs before you push:

```bash
composer run ci:check
```

That is ESLint, Prettier, `vue-tsc`, Pint, PHPStan, and Pest. The individual pieces:

```bash
composer run test        # Pint check → PHPStan → Pest
php artisan test         # tests only
php artisan test --filter=BuildApplicationControllerTest

bun run lint          # ESLint, with --fix
bun run format        # Prettier, writes changes
bun run format:check  # Prettier, check only
bun run types:check   # vue-tsc

vendor/bin/pint       # PHP code style, with --fix
```

### If PHPStan complains about model properties

`_ide_helper_models.php` is generated and git-ignored. If static analysis reports errors about properties that clearly exist, regenerate it before assuming you broke something:

```bash
composer run helpers
```

### Tests

Tests live in `tests/Feature` and `tests/Unit` and use Pest. Create new ones with:

```bash
php artisan make:test --pest SomeFeatureTest
```

Please add or update tests for behaviour and logic changes. Copy-only and styling changes do not need them.

## Open a pull request

1. Branch from `main`: `git checkout -b short-description`.
2. Make your change, and add tests if it changes behaviour.
3. Run `composer run ci:check` and make sure it passes.
4. Push and open a pull request against `main`.

A focused pull request — one change, one concern — is much easier to review and far more likely to land. If you are unsure whether something is wanted, open an issue first; that costs you less time than a large pull request that gets declined.

CI runs on every push and pull request against `main` and `develop`.

## Areas that always need help

- **Translations.** The app ships English and Spanish. New strings need both `lang/en.json` and `lang/es.json`; improving existing Spanish copy is genuinely useful.
- **New services and starter kits.** These come from Laravel's own installer, so adding one usually means a dependency bump plus entries in `BuildOptions`.
- **Documentation.** This README, the glossary, and the comparison pages are all maintained by hand. Fixing something that confused you is a real contribution.
- **Tests.** Especially around `app/Actions`, the MCP tools, and validation in `app/Http/Requests`.
- **Accessibility.** The frontend is Vue with shadcn-vue primitives. Keyboard navigation, focus management, and contrast issues are all fair game.

## Code style

- PHP follows [Pint](https://laravel.com/docs/pint) — run `vendor/bin/pint` before committing.
- PHP 8 conventions: constructor property promotion, explicit return types and parameter types, curly braces on every control structure, PHPDoc over inline comments.
- Vue and TypeScript follow the existing files in `resources/js/`. Run `bun run lint` and `bun run format`.
- Follow the conventions of the file you are editing rather than introducing a new style.

## Reporting security issues

If you find a security vulnerability, please [open an issue](https://github.com/wilsenhc/laravel-charter/issues) rather than posting it publicly, so a fix can be prepared before disclosure.
