<p align="center">
  <a href="https://laravelcharter.com/application"><strong>Open the builder →</strong></a>
  &nbsp;·&nbsp;
  <a href="#self-hosting">Deploy it yourself</a>
  &nbsp;·&nbsp;
  <a href="https://github.com/wilsenhc/laravel-charter">Star on GitHub</a>
</p>

![](https://banners.beyondco.de/Charter%20for%20Laravel.png?theme=light&pattern=architect&style=style_1&description=A+visual+command+builder+for+Laravel+Sail&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

# Charter for Laravel

**Visually configure a Laravel Sail application or package, then generate a ready-to-run command.**

[![Tests](https://github.com/wilsenhc/laravel-charter/actions/workflows/tests.yml/badge.svg)](https://github.com/wilsenhc/laravel-charter/actions/workflows/tests.yml)
[![Linter](https://github.com/wilsenhc/laravel-charter/actions/workflows/lint.yml/badge.svg)](https://github.com/wilsenhc/laravel-charter/actions/workflows/lint.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.md)
[![Hosted at](https://img.shields.io/badge/hosted%20at-laravelcharter.com-ff4154)](https://laravelcharter.com)

Picking the right options for a new Laravel project means holding a long list of installer flags in your head. Charter turns that list into a form: choose your services, starter kit, and tooling, then copy a single command that scaffolds the project with [Laravel Sail](https://laravel.com/docs/sail) already installed and configured.

No account, no install, nothing to clean up.

## What Charter does

Charter is a UI and a thin, transparent layer over Laravel's own tooling. It does not replace the official installer — it drives `laravel new` and `sail:install` with the flags you picked, and shows you the exact script it will run.

**Two builders, one hosted app:**

| Builder | What it scaffolds |
| --- | --- |
| **[Application builder](https://laravelcharter.com/application)** | Scaffold a Laravel Sail application: PHP version, database driver, Docker services, starter kit, auth provider, testing framework, JavaScript runtime, and flags for Teams, Boost, devcontainer, and custom starter kits. |
| **[Package builder](https://laravelcharter.com/package)** | Bootstrap a Laravel package with the pieces you want — config, routes, views, translations, migrations, assets, commands, facade, and Boost skill — plus author and `vendor/package` metadata. |

Every option the builders expose comes from the official `laravel new` installer, so the list stays honest as Laravel grows.

<details>
<summary><strong>Supported options today</strong></summary>

| Option | Values |
| --- | --- |
| PHP | 8.5, 8.4, 8.3 |
| Database driver | MySQL, MariaDB, PostgreSQL, SQLite, SQL Server, or none |
| Services | MySQL, MariaDB, PostgreSQL, MongoDB, Redis, Valkey, Memcached, Meilisearch, Typesense, RustFS, Mailpit, Mailtrap, RabbitMQ, Selenium, Soketi |
| Starter kits | None, Livewire, Vue, React, Svelte, or a custom starter kit URL |
| Authentication | None, Laravel, or WorkOS |
| Testing | Pest or PHPUnit |
| JavaScript runtime | npm, pnpm, Bun, or Yarn |
| Package features | Config, routes, views, translations, migrations, assets, commands, facade, Boost skill |

</details>

## Try it

Open the builder, configure your options, and copy the command:

```bash
curl -fsSL 'https://laravelcharter.com/application/build?name=my-app&services=pgsql,redis&frontend=none&database=pgsql&testing=pest&auth=laravel&boost&php=8.5' | bash
```

**→ [laravelcharter.com/application](https://laravelcharter.com/application)**

The script runs the real installer in a container, installs Sail with your services, prints the next steps, and hands the directory back to you. The site itself only renders the command. Builds requested by non-browser clients (such as `curl` or an MCP agent) are counted anonymously in aggregate — no personal or identifying information is collected, and you can read exactly what is gathered in the [privacy notice](https://laravelcharter.com/privacy).

## Examples

Each link below returns the exact script Charter generates for that configuration. The builder UI does not currently remember your selections in the URL, so pick the same values from the dropdowns if you would rather configure it visually.

**Laravel + PostgreSQL + Redis** — PHP 8.5, PostgreSQL driver, Pest, Boost

```bash
curl -fsSL 'https://laravelcharter.com/application/build?name=my-app&services=pgsql,redis&frontend=none&database=pgsql&testing=pest&auth=laravel&boost&php=8.5' | bash
```

**Laravel + Vue + authentication + Pest** — PHP 8.5, MySQL, Vue starter kit, Bun, Pest, Boost

```bash
curl -fsSL 'https://laravelcharter.com/application/build?name=my-app&services=mysql&frontend=vue&javascript=bun&testing=pest&auth=laravel&boost&php=8.5' | bash
```

**Laravel + Livewire class components** — PHP 8.5, MySQL, Livewire with class components, Pest, Boost

```bash
curl -fsSL 'https://laravelcharter.com/application/build?name=my-app&services=mysql&frontend=livewire&livewire-class-components&testing=pest&auth=laravel&boost&php=8.5' | bash
```

**A package with commands, migrations, routes, and config** — PHP 8.5

```bash
curl -fsSL 'https://laravelcharter.com/package/build?name=my-package&features=config,routes,migrations,commands&php=8.5' | bash
```

> **Review before you pipe.** The generated output is a `bash` script served over HTTPS. Read it, or swap `| bash` for `curl -o build.sh && less build.sh`, if that is your habit.

## Use it from your AI agent (MCP)

Charter ships a [Model Context Protocol](https://modelcontextprotocol.io) server so an agent can scaffold projects through the same code path as the web UI.

- **Endpoint:** `https://laravelcharter.com/mcp/charter`
- **Tools:** `build-application`, `build-package`

Setup instructions for Claude Code, Claude Desktop, and other MCP clients are on the [MCP page](https://laravelcharter.com/mcp). The server is also part of this codebase — see [`app/Mcp`](app/Mcp) — so you can self-host it alongside Charter.

## Learn the options

- **[Glossary](https://laravelcharter.com/glossary)** — what each service, starter kit, and tool actually does.
- **[Comparisons](https://laravelcharter.com/compare/mysql-vs-pgsql)** — MySQL vs MariaDB vs PostgreSQL, Redis vs Valkey, Pest vs PHPUnit, and more.
- **[Usage statistics](https://laravelcharter.com/stats)** — anonymous aggregate counts of what the community builds. No personal data is collected.

## Self-hosting

Charter is MIT licensed and you can run it yourself. **[deployment.md](docs/deployment.md) is the canonical, tested deployment guide** — it covers Laravel Cloud, which is what `laravelcharter.com` runs on: database, runtime, the queue worker, environment variables, domains, backups, upgrades and rollback.

The short version:

1. Provision a **PostgreSQL** database. SQLite does not work in production — its filesystem is ephemeral and resets on every deploy.
2. Set the **PHP version to 8.5** and turn **Use Octane as runtime** on.
3. **Replace the build command** so it uses Bun instead of npm — Cloud defaults to npm for Laravel apps, and a `bun.lock` alone will not change that.
4. Add a deploy command that runs `php artisan migrate --force` and the two content seeders.
5. Add a **queue worker** background process for the anonymous build-statistics jobs.

Two things worth knowing before you start:

- [`compose.yaml`](compose.yaml) is the **local development** environment (Sail image, source bind mount, file watching). It is not a production recipe, and should not be deployed as one.
- If you deploy somewhere other than Laravel Cloud, the [runtime requirements](docs/deployment.md#runtime-requirements) still apply — Charter needs a web process, one queue worker, and no task scheduler at all.

If you deploy Charter yourself, please [open an issue](https://github.com/wilsenhc/laravel-charter/issues) describing what you had to work around — it directly shapes the guide.

## Development setup

> For contributors and maintainers. To *use* Charter, you do not need any of this — open [laravelcharter.com](https://laravelcharter.com/application) and copy the command.

### Requirements

- **PHP 8.5** — pinned in `.php-version`, and what CI runs
- [Laravel 13](https://laravel.com) and [Composer 2](https://getcomposer.org)
- [Bun](https://bun.sh) — this project uses Bun, not npm
- A database for the app, and one for the test suite (see `.env.testing`).

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite   # .env.example defaults to SQLite; Laravel will not create it for you
php artisan migrate

bun install
bun run build
```

`composer setup` runs the same steps in one shot, minus the `touch`.

#### Using PostgreSQL instead

Match CI by pointing `.env` at a PostgreSQL instance:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=charter
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

The repository also ships a Sail-based stack ([`compose.yaml`](compose.yaml), [`docker/`](docker)) for running the app itself inside containers.

### Running the app

```bash
composer run dev
```

Starts the PHP server, a queue listener, log tailing, and the Vite dev server together.

### Tests, linting, and types

```bash
composer run test        # Pint check → PHPStan → Pest (the full gate)
php artisan test         # tests only
php artisan test --filter=BuildApplicationControllerTest   # one test
```

`composer run test` runs Pint and PHPStan **before** the suite, so it fails on style and static-analysis problems as well as test failures.

The suite runs against the database named in `.env.testing`, which is git-ignored. Point it at a database you are happy for the suite to reset.

### Frontend

```bash
bun run lint          # ESLint, with --fix
bun run format        # Prettier, writes changes
bun run types:check   # vue-tsc
```

### Everything CI checks

```bash
composer run ci:check
```

### Regenerating IDE helper files

`composer run test` and PHPStan read `_ide_helper_models.php`, which is generated and git-ignored. If PHPStan reports errors about model properties that look wrong, regenerate it first:

```bash
composer run helpers
```

## Contributing

Contributions are welcome — translations, new services and starter kits, documentation, tests, and accessibility work are all useful. See **[CONTRIBUTING.md](CONTRIBUTING.md)** for the full guide, and start with issues labelled [`good first issue`](https://github.com/wilsenhc/laravel-charter/labels).

## Project status

Charter is actively maintained and in use in production at [laravelcharter.com](https://laravelcharter.com). The application builder, package builder, and MCP server are stable and covered by tests. Self-hosting is documented in [deployment.md](docs/deployment.md). For anything not covered here, [open an issue](https://github.com/wilsenhc/laravel-charter/issues).

## Support

Charter is free and MIT licensed, and it always will be — sponsoring buys maintenance time, never features.

[![PayPal](https://img.shields.io/badge/support%20me-PayPal-003087?logo=paypal)](https://paypal.me/wilsenjhc)

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md).

## Acknowledgements

This project is not a fork but builds upon the ideas and implementations of two projects:

- [laravel/sail-server](https://github.com/laravel/sail-server) — the official Laravel Sail server package.
- [rogerio-pereira/laravel-sailor](https://github.com/rogerio-pereira/laravel-sailor) — a community project for scaffolding Laravel Sail applications.

We give credit where it's due and recognize the contributions of the original authors.

## Credits

- [All Contributors](https://github.com/wilsenhc/laravel-charter/graphs/contributors)

## Security

If you discover a security vulnerability, please [open an issue](https://github.com/wilsenhc/laravel-charter/issues) rather than posting publicly.
