# Deploying Charter for Laravel

This is the canonical, supported way to run Charter for Laravel in production. It targets [Laravel Cloud](https://cloud.laravel.com), which is what `laravelcharter.com` runs on.

If you deploy somewhere else — a VPS, Forge, a container platform — the [runtime requirements](#runtime-requirements) section still applies. Everything else in this document is Cloud-specific.

> **Looking at `compose.yaml` instead?** That file is the local development environment: it bind-mounts your working tree and runs Octane with `--watch`. It is not a production recipe, and deploying it will not give you a working site.

---

## What Laravel Cloud handles for you

Do not try to configure any of this yourself:

- **TLS certificates**, issued and renewed automatically for every custom domain.
- **Zero-downtime releases.** The previous deployment is gracefully terminated before the new one goes live.
- **Queue worker restarts** after every deployment. Do not run `php artisan queue:restart`.
- **The Octane runtime itself**, once you enable the toggle (see below). Cloud builds and serves your app with Octane and FrankenPHP.
- **Log collection.** Application and access logs appear in the environment's **Logs** tab.
- **Database backups** for managed databases.
- **Edge security.** `X-Frame-Options: DENY` and `X-Content-Type-Options: nosniff` are set by default; HSTS, WAF and rate limiting are available as toggles.
- **Scale to zero** on Flex compute, so an idle site costs nothing.

There are also four commands Cloud handles or forbids. Running them is either redundant or harmful:

| Command | Why not |
| --- | --- |
| `php artisan queue:restart` | Cloud restarts workers automatically. |
| `php artisan horizon:terminate` | Cloud manages Horizon during deployments. |
| `php artisan optimize:clear` | Clears the cache and can cause unexpected behaviour, especially around the queue. |
| `php artisan storage:link` | The symlink will not persist — the filesystem is ephemeral. |

## What Cloud will *not* do for you

These are the parts that are specific to Charter and easy to miss. Each is covered in a step below.

1. **Use Bun.** Cloud runs `npm` by default for Laravel applications. Your lockfile does not change that.
2. **Run migrations.** Nothing migrates until you write a deploy command.
3. **Seed the glossary and comparison content.** Without it, `/glossary`, `/compare/*` and `/llms.txt` are empty or 404.
4. **Set `APP_KEY`.** Cloud does not generate one for you.
5. **Run a queue worker.** Charter has two queued jobs and no worker until you add one.

## The canonical production configuration

This is the verified configuration behind `laravelcharter.com`. Copy it rather than inventing values:

| Setting | Value |
| --- | --- |
| PHP version | 8.5 |
| Node version | 24 |
| Use Octane as runtime | On |
| Use Inertia SSR | On |
| Scheduler | Off |
| Scale to zero (hibernation) | On |
| Push to deploy | On |
| Domain | `laravelcharter.com`, hostname/SSL/origin all verified |
| Background process | 1 × `php artisan queue:work database --sleep=10 --quiet` |
| Environment variables set explicitly | `APP_KEY`, `WEBMCP_ORIGIN_TRIAL_TOKEN` |

Everything else — `APP_ENV`, `APP_DEBUG`, `APP_URL`, `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` — resolves to a safe Laravel default or is injected by Cloud. The database connection variables are injected when the database is attached.

---

## Runtime requirements

Charter's production process model is deliberately small. It was verified against the code, not assumed from a standard Laravel deployment:

| Process | Required | Detail |
| --- | --- | --- |
| Web (Octane + FrankenPHP) | Yes | Serves all HTTP traffic. Cloud runs this for you once Octane is enabled. |
| Queue worker | Yes | Two jobs: `RecordApplicationBuildStatJob`, `RecordPackageBuildStatJob`. Each is a single insert that records anonymous aggregate statistics. |
| Task scheduler | **No** | `routes/console.php` contains only the stock `inspire` command and there is no `Schedule::` call anywhere in the codebase. Leave the Scheduler toggle off. |
| Inertia SSR | **Yes** | The canonical deployment has it on. See [Inertia SSR](#inertia-ssr). Without it the app still works, falling back to client-side rendering. |
| Vite dev server | **No** | Assets are built at deploy time. |
| Cache / session server | **No** | Both use the database, which keeps the infrastructure to a single resource. |

### Required environment variables

| Variable | Value | Why |
| --- | --- | --- |
| `APP_KEY` | generated | Required. Cloud does not create one. |
| `WEBMCP_ORIGIN_TRIAL_TOKEN` | optional | A Chrome Origin Trial token bound to one origin and with an expiry. A token minted for another domain is a dead `<meta>` tag. The MCP endpoint works without it. |
| `INERTIA_SSR_ENABLED` | leave unset, or `true` | Defaults to `true`, which is correct when the **Use Inertia SSR** toggle is on. See [Inertia SSR](#inertia-ssr). |
| `APP_URL` | do not set | Cloud injects it from your primary domain. It already drives `url()`, the sitemap, canonical tags and the MCP endpoint. Setting it yourself is only necessary to override the default. |
| `CACHE_STORE` | `database` (default) | **Must be shared.** Both statistics jobs use `Cache::add()` as a dedup lock; a per-process store such as `array` or `file` silently degrades it to per-worker, producing duplicate rows under Octane. Set it explicitly rather than relying on the default. |
| `SESSION_DRIVER` | `database` (default) | Shared across replicas. |
| `QUEUE_CONNECTION` | `database` (default) | Must match the background worker you configure. |
| `SESSION_SECURE_COOKIE` | `true` (optional) | Explicitly secure behind Cloud's TLS proxy. Laravel already infers this from the request scheme. |

`DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` and the rest of the connection details are injected automatically once you attach a database, so do not set them by hand. `APP_ENV` defaults to `production` and `APP_DEBUG` to `false`.

Custom variables take priority over anything Cloud injects, so you can always override a value.

---

## Step 1 — Create the application

Point Cloud at the repository and the `main` branch.

```sh
composer global require laravel/cloud-cli
cloud auth -n
cloud ship -n
```

`cloud ship` asks for the repository and branch. Discover its options with `cloud ship -h`. If the application already exists:

```sh
cloud application:list --json -n
```

Each environment receives a free `laravel.cloud` domain after its first successful deployment. Cloud sets `X-Robots-Tag: noindex, nofollow` on `*.laravel.cloud` domains only, so the site stays out of search results until you attach a real domain.

## Step 2 — Attach a database

Attach **Serverless Postgres** (or Laravel MySQL) from the infrastructure canvas. **SQLite will not work**: the environment filesystem is ephemeral and resets on every deployment, sleep, and infrastructure migration, so a SQLite file loses its data. This is why `.env.example` ships with `DB_CONNECTION=sqlite` for local work only.

The database must be in the same region as your environment.

If you are migrating a local SQLite database, the docs recommend [pgloader](https://pgloader.readthedocs.io/) or `sqlite3 .dump` piped into `psql`. Be aware that PostgreSQL enforces foreign keys and is stricter about types than SQLite, so invalid data can surface only after the move.

## Step 3 — Runtime settings

On the environment's **App cluster**:

| Setting | Value | Notes |
| --- | --- | --- |
| PHP version | **8.5** | Cloud supports 8.2–8.5 and defaults to 8.5 for new environments. `composer.json` requires `^8.5`, so older versions will fail to install. A re-deployment is required after changing this. |
| Node version | **24** | Needed to build the frontend and to run Inertia SSR. |
| **Use Octane as runtime** | **On** | Cloud builds and serves your app with Octane and FrankenPHP. `laravel/octane` is already a production dependency. |
| **Use Inertia SSR** | **On** | Matches the canonical deployment. See [Inertia SSR](#inertia-ssr). |
| Scheduler | Off | Charter has no scheduled tasks. |

## Inertia SSR

Charter renders its pages through Inertia, and the canonical deployment **uses Inertia SSR**: the response for `/application` arrives as roughly 80 KB of fully server-rendered markup rather than an empty `<div id="app">`.

This works because three things line up:

1. The build script ends with `vite build --ssr`, so `bootstrap/ssr/app.js` exists.
2. The **Use Inertia SSR** toggle is on, so Cloud runs the SSR server.
3. `INERTIA_SSR_ENABLED` is unset, and `config/inertia.php` defaults it to `true`.

If you do **not** enable the Cloud toggle, set `INERTIA_SSR_ENABLED=false` explicitly. Without the server, each full-page load attempts a loopback POST to `127.0.0.1:13714`, which is refused almost immediately and falls back to client-side rendering. Nothing breaks and no listener logs it, so it is only a small wasted round trip per page view — but be aware the failure is silent.

Keep SSR on unless you have a reason to turn it off. It adds a Node process to the request path for every full page load.

## Step 4 — Build command

**This is the step people miss.** Laravel Cloud installs and runs `npm` by default for Laravel applications, even when a `bun.lock` is present. Bun auto-detection only applies to JavaScript frameworks such as Next.js and Nuxt. For a Laravel app, only your build command decides.

The default build command is roughly:

```sh
composer install --no-dev && npm run build
```

Replace it entirely — if you leave the default `npm` lines in place, they will run alongside the Bun commands:

```sh
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm install -g bun
bun install --frozen-lockfile
bun run build:ssr
```

Notes:

- `--frozen-lockfile` makes the build fail rather than silently updating `bun.lock` when it disagrees with `package.json`. Keep it in CI-facing builds.
- `bun run build:ssr` and `bun run build` run the same commands. Use the `:ssr` variant when SSR is enabled so the intent is obvious.
- `bun run build` shells out to `php artisan generate:build-options`, then type-checks with `vue-tsc`, builds the client bundle, copies `public/build/manifest.webmanifest` to `public/manifest.webmanifest`, and builds the SSR bundle. It needs PHP, which is present.
- Build commands must finish within 15 minutes.

### Caching the framework's caches

Laravel's own deployment guide recommends appending `php artisan optimize` to the **build** command, not the deploy command, so cached config, routes and compiled views are baked into the deployment:

```sh
php artisan optimize
```

The canonical `laravelcharter.com` deployment does **not** currently do this, and works correctly without it — Laravel simply recompiles on boot. Adding it is an optimisation, not a fix. Be aware that it runs `config:cache`, which freezes the values of every environment variable as of build time, so only add it when you are confident the build environment reflects the runtime one.

## Step 5 — Deploy command

Deploy commands run immediately before a deployment goes live. They must be non-interactive and finish within 15 minutes. Any filesystem changes they make are discarded.

```sh
php artisan migrate --force
php artisan llms:clear
php artisan db:seed --class=GlossaryTermSeeder --force
php artisan db:seed --class=ComparisonSeeder --force
```

The first two lines match the canonical deployment. `llms:clear` drops the cached `/llms.txt` output, which is regenerated per request from the glossary and comparison tables — it is the app-specific example of a deploy-time cache clear.

**Why the seeders.** `GlossaryTermSeeder` and `ComparisonSeeder` use `updateOrCreate`, so they are idempotent and safe on every deploy. They read `database/data/glossary-terms.json` and `database/data/comparisons.json`. Without them `/glossary` renders an empty list, every `/glossary/{term}` and `/compare/{slug}` returns 404, the sitemap loses those URLs, and `/llms.txt` returns nothing useful. The seeder reads those files with a `?: '[]'` fallback, so a missing file fails silently rather than loudly.

The live site currently carries this content because it was seeded once by hand, not on every deploy. That means it survives code deploys but **not a database being recreated**. Adding these two lines makes that self-healing.

Do **not** run `DatabaseSeeder` — it also inserts 300 fake application statistics and 100 fake package statistics, which is demo data.

Do not try to pass `--class` twice in one command:

```sh
# Wrong — --class is a single value, so only ComparisonSeeder runs.
php artisan db:seed --class=GlossaryTermSeeder --class=ComparisonSeeder --force
```

Run them as two separate commands, as above.

## Step 6 — Environment variables

In the environment's **Environment variables** section, or with the CLI:

```sh
cloud environment:variables --json --action=set --key=APP_ENV --value=production -n --force
```

Set the variables from [Required environment variables](#required-environment-variables). Generate the key locally:

```sh
php artisan key:generate --show
```

Redeploy after changing variables — they are read at build time by `php artisan optimize`.

Leave `LOG_CHANNEL` and `LOG_STACK` at their defaults. If you enable Nightwatch, Cloud injects `LOG_CHANNEL=stack` and `LOG_STACK=laravel-cloud-socket,nightwatch` for you.

## Step 7 — Queue worker

Charter dispatches two jobs whenever a build script is generated outside a browser — that is, by `curl`, by an MCP client, or by the WebMCP browser API. They record anonymous aggregate statistics and are what makes the [statistics page](https://laravelcharter.com/stats) work.

Add a **background process** to the App cluster using the **queue worker** configuration:

| Field | Value |
| --- | --- |
| Connection | `database` |
| Queue | default |
| Processes | `1` |
| Sleep | `10` |

This produces `php artisan queue:work database --sleep=10 --quiet`, which is what the canonical deployment runs. `--sleep=10` means a dispatched job waits up to ten seconds to be picked up, so new statistics appear on `/stats` with that much delay.

One process is plenty: the jobs are single inserts, and each is deduplicated for ten minutes by a `Cache::add()` lock before it runs. Leave `timeout` and `tries` unset — `queue:work` defaults to a 60-second timeout, safely under the queue's 90-second `retry_after`, and a single attempt.

Cloud restarts workers after every deployment, so `php artisan queue:restart` is neither needed nor wanted.

If you need `--max-jobs` or `--max-time`, which the queue worker form does not expose, use the **Custom worker** tab instead:

```sh
php artisan queue:work --queue=default --tries=3 --timeout=60 --max-time=3600
```

### Scale to zero and your queue

Scale to zero is **on by default** for Flex compute, and a background process on the App cluster sleeps with it. Cloud's documentation is explicit that a job still running when the sleep timeout elapses may be interrupted, and recommends managed queues for queued jobs on environments that scale to zero.

For Charter this is an acceptable trade-off in both directions: the interrupted job is one anonymous statistics insert, and the worst case is a slightly undercounted statistics page. If you would rather never lose one:

1. `composer require aws/aws-sdk-php`
2. Attach a **managed queue** from the canvas and deploy.

Cloud then sets `QUEUE_CONNECTION=cloud` and every job routes there. Managed queues are billed per worker-second and per operation (standard operations are $1 per million). They also support failed-job inspection and retry from the dashboard; the `queue:failed`, `queue:retry` and `queue:clear` Artisan commands do not work with them.

## Step 8 — Deploy

Push to `main` and Cloud deploys automatically — push to deploy is enabled by default on all environments. Or trigger one manually:

```sh
cloud deploy <application> <environment> -n --open
cloud deploy:monitor -n
```

Always monitor. If a deploy fails, the deployment log explains why.

You can also deploy a specific commit through the deploy hook, which is the basis of the rollback procedure below:

```sh
curl -X POST "https://your-deploy-hook-url?commit_hash=abc123def456"
```

---

## Step 9 — Verify

Run through this after the first deploy. Each command is a real request against the public URL; substitute your domain.

```sh
# Liveness. Laravel's /up route answers 200 and does not touch the database.
curl -i https://charter.example.com/up

# The homepage redirects to the default-locale application builder.
curl -i https://charter.example.com/

# The application builder renders and assets resolve (200, not a Vite manifest error).
curl -s https://charter.example.com/application | grep -o 'build/assets/[^"]*\.js' | head -3

# The generated script is produced and is real shell.
curl -s 'https://charter.example.com/application/build?name=verify&services=pgsql,redis&frontend=none&database=pgsql&testing=pest&auth=laravel&boost&php=8.5' | head -5

# The package builder.
curl -s 'https://charter.example.com/package/build?name=verify&features=config,routes,migrations,commands&php=8.5' | head -5

# Glossary and comparisons are seeded, not empty.
curl -s https://charter.example.com/glossary | grep -c 'glossary/'
curl -s -o /dev/null -w '%{http_code}\n' https://charter.example.com/compare/mysql-vs-pgsql

# Sitemap lists the seeded content.
curl -s https://charter.example.com/sitemap.xml | grep -c '<loc>'

# The service worker and web app manifest exist — a 404 here breaks PWA install.
curl -s -o /dev/null -w 'sw.js %{http_code}\n'          https://charter.example.com/sw.js
curl -s -o /dev/null -w 'manifest %{http_code}\n'      https://charter.example.com/manifest.webmanifest

# The MCP endpoint answers JSON-RPC. This exercises the real MCP path,
# not the browser UI.
curl -s -X POST https://charter.example.com/mcp/charter \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-06-18","capabilities":{},"clientInfo":{"name":"verify","version":"1.0"}}}'

# An MCP tool call end to end.
curl -s -X POST https://charter.example.com/mcp/charter \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"build-application","arguments":{"name":"verify","services":["pgsql"],"php":"8.5","testing":"pest","boost":true}}}'
```

Then confirm, in order:

1. **The queue worker is alive.** Generate a build with `curl` (above), then check the **Logs** tab for worker output. Build statistics appear on `/stats` within a few seconds.
2. **The database is reachable from the app.** `/up` does not query it, so use a database-backed page — `/stats` or `/glossary` — to prove the connection works.
3. **No debug output.** With `APP_DEBUG=false`, an unhandled exception must render a generic error page, never a stack trace.

## Step 10 — Custom domain

Add the domain from the environment's **Network settings → Add domain**. You choose wildcard support, redirect behaviour, downtime preference, and whether DNS is Cloudflare-managed; Cloud then tells you the exact DNS records to create.

The usual records are an `A` record (or a `CNAME` when using Cloudflare DNS), plus `TXT _cf-custom-hostname` for ownership validation and a `_acme-challenge` record for certificate validation. Copy the values exactly as Cloud shows them — they are generated per environment.

Two traps:

- If DNS is on Cloudflare, `_acme-challenge` records must be **DNS only** (grey cloud). Proxying them blocks certificate issuance.
- If you use wildcard support, keep the `_acme-challenge` CNAME permanently — removing it breaks automatic renewal.

Cloud retries verification for about 12 hours, and most domains connect within 15 minutes. Once connected, set `APP_URL` to your real domain and redeploy.

SSL is automatic and is **not** applied to `*.laravel.cloud` free domains beyond the noindex header.

---

## Backups and restore

Cloud manages backups for its managed databases, including point-in-time recovery. Confirm the automatic backup status and retention window on the database's canvas in the dashboard.

Take a manual snapshot before any risky change — a migration that drops columns, or an upgrade:

1. Open the database on the environment's infrastructure canvas and create a snapshot.
2. Restores are initiated from the same place; a restore creates a new database, so point the environment at it and redeploy.

Restore-into-a-discarded-environment is also available through the Cloud API (`POST /database-clusters/{cluster}/restores`), but the dashboard is the documented path.

**Your own backups.** If your statistics or content ever matter to you, export them. Statistics are the only data Charter generates at runtime:

```sh
pg_dump "postgresql://user:password@host/dbname?sslmode=require" --data-only --table=application_stats --table=package_stats > stats.sql
```

Glossary and comparison content is versioned in `database/data/*.json`, so it is restored by seeding, not by backup.

## Upgrades and rollback

**Upgrade.** Push to `main`; Cloud builds, runs your deploy command, and swaps traffic over with zero downtime. Database migrations run as part of that deploy command, so:

1. Take a database snapshot first if the migration is destructive.
2. Deploy.
3. Monitor the deployment log.
4. Run the [verification](#step-9--verify) commands.

**Rollback.** Reverting code by itself is not enough if a migration already ran, so treat these as one change. The deploy hook is the documented way to redeploy a specific commit, but it is **disabled by default** — enable **Deploy hook** under **Settings → Deployments** first, then:

```sh
curl -X POST "https://your-deploy-hook-url?commit_hash=<previous-good-sha>"
```

Cloud does not document rolling back to a previous *build* as a dashboard action, so the hook is the route. Without it, the fallback is to revert the commit and push, accepting that the deploy command runs again.

If the migration is not backward compatible, restore the database snapshot first, then redeploy the previous commit. Deploying an older commit while the newer schema is in place is what causes most rollback failures.

## Operating notes

- **Logs.** The **Logs** tab under **Monitoring** holds both application logs and Cloud's access logs, filterable by type. Background-process output appears in the same place. Retention depends on your plan — one day on Starter, seven on Growth, thirty on Business and Enterprise — so older entries are eventually deleted. Log drains to S3 are Enterprise-only.
- **Sleep.** Flex compute scales to zero after a period without inbound traffic and wakes in under 500 ms on current sizes. Free `laravel.cloud` domains carry `X-Robots-Tag: noindex, nofollow`, so bots do not keep it awake.
- **Request timeout.** Configurable between 5 and 60 seconds per environment. Charter's endpoints are fast; the generated shell scripts are a few kilobytes.
- **Autoscaling.** `None`, `Custom` (a replica range) or `Unlimited`. Cloud sizes replicas from in-flight requests, estimating roughly 30 MB per PHP worker. Replica counts multiply your background processes — a worker configured with 4 processes on 3 replicas is 12 `queue:work` processes.
- **Memory limit.** Cloud allocates PHP memory as a percentage of instance memory. To pin it, define a `memory_limit` variable rather than editing source.

## Troubleshooting

| Symptom | Cause and fix |
| --- | --- |
| Build fails on `npm` or a missing `bun` | The default build command is still in place. Replace it wholesale with the commands in [Step 4](#step-4--build-command). |
| `Your lock file does not contain a compatible set of packages` | `composer.json` requires `^8.5`; the environment is on an older PHP. Set the environment to 8.5 and redeploy. |
| Assets 404, or "Unable to locate file in Vite manifest" | The build did not run or `public/manifest.webmanifest` was not produced. Confirm the build command completed and that no `npm` lines remain. |
| `/glossary` is empty, `/compare/*` 404s | The seeders did not run. Add both to the deploy command and redeploy. |
| `/llms.txt` is empty | Same cause — it reads the seeded glossary and comparison tables. |
| Statistics never appear on `/stats` | No queue worker, or `QUEUE_CONNECTION` does not match it. Allow for up to the worker's `--sleep` interval before expecting them. Check the **Logs** tab for worker output. |
| Duplicate statistics rows | `CACHE_STORE` is not `database`. A per-process cache store breaks the dedup lock. |
| Pages render but arrive as an empty `<div id="app">` | Inertia SSR is not running. Turn on the **Use Inertia SSR** toggle and rebuild so `bootstrap/ssr/app.js` is produced, or set `INERTIA_SSR_ENABLED=false` to make the fallback explicit. |
| Maintenance mode does not persist across replicas | Use `APP_MAINTENANCE_DRIVER=cache` with `APP_MAINTENANCE_STORE=database`. |

### A note on Octane and long-lived workers

`config/octane.php` warms the `db` and `db.factory` services at worker boot and enables the `DisconnectFromDatabases` and `CollectGarbage` listeners on `OperationTerminated`. That combination is deliberate: because the connection is pre-resolved at boot, it must be released after each request or a long-lived worker holds it open. If you disable either half, re-check the other.

## Known gaps

Laravel Cloud's documentation does not cover the following. Each was checked against the published docs rather than assumed, and none currently affects Charter's behaviour:

- **HTTP response buffering, request timeouts and body-size limits** at the edge are not documented. Charter's MCP tools return ordinary JSON responses rather than streamed ones, so streaming support is not currently exercised — but a future streaming tool would need empirical verification.
- **Server-Sent Events and long-lived connections** are not documented as a general capability. The only long-lived-connection product is managed WebSockets, which is not a passthrough for arbitrary streaming responses.
- **Rolling a deployment back** to a previous build is not documented as a dashboard action. The deploy hook's `commit_hash` parameter is the documented way to redeploy a specific commit.
- **The default sleep timeout** duration is not published; only the API field is.

If one of these becomes a problem, [Laravel Cloud support](https://cloud.laravel.com/docs/support) is the route.
