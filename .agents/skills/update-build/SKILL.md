---
name: update-build
description: "Updates the resources/stubs/build-application.sh and resources/stubs/build-package.sh Blade templates to stay in sync with upstream changes from laravel/installer and laravel/sail. Run this when new options or arguments are added to the `laravel new`, `laravel package`, or `sail:install` commands, or when updating those packages."
license: MIT
metadata:
  author: wilsenhc
---

# Update Build Stubs

There are two independent tracks:

- **Application Build** (`build-application.sh`, `BuildController::show()`) — the `laravel new` + `sail:install` commands
- **Package Build** (`build-package.sh`, `BuildController::package()`) — the `laravel package` command

Each track has its own options, controller method, form request, frontend page, and tests. Run the workflow below for **each track separately**.

## Workflow

### 1. Upgrade packages

```bash
vendor/bin/sail composer upgrade laravel/installer laravel/sail
```

### 2. Check if versions changed

Read `composer.lock` entries for `laravel/installer` and `laravel/sail` to confirm the `version` or `source.reference` changed. If neither changed, stop here.

### 3. Read vendor command files

Read the following files to identify all current options and arguments:

- `vendor/laravel/installer/src/NewCommand.php` — `configure()` method (defines `addArgument()` and `addOption()` calls for `laravel new`)
- `vendor/laravel/installer/src/PackageCommand.php` — `configure()` method (defines `addArgument()` and `addOption()` calls for `laravel package`)
- `vendor/laravel/sail/src/Console/InstallCommand.php` — `$signature` property (defines options for `sail:install`)

### 4a. Compare against build-application.sh

Read `resources/stubs/build-application.sh` and `app/Http/Controllers/BuildController.php` (the `show()` method that builds `$options` and passes variables to the Blade render).

Compare every option/argument from the vendor commands (`laravel new`, `sail:install`) against what `BuildController::show()` passes in `$options` and what `build-application.sh` uses as template variables.

### 4b. Compare against build-package.sh

Read `resources/stubs/build-package.sh` and `app/Http/Controllers/BuildController.php` (the `package()` method that builds `$options` and passes variables to the Blade render).

Compare every option/argument from `vendor/laravel/installer/src/PackageCommand.php` (`configure()` method) against what `BuildController::package()` passes in `$options` and what `build-package.sh` uses as template variables.

Also check if the generated `configure.php` boilerplate (inside the scaffolded package) has gained or lost options — `PackageCommand` passes feature/metadata flags to `configure.php` as shell arguments, so any change there could break the round-trip. Inspect `vendor/laravel/installer/src/stubs/package/configure.php` (or wherever the stub lives) for changed flag names.

### 5. Report new options

For each track, ONLY list options/arguments that exist in the vendor commands but are NOT yet handled in the respective controller method or stub. For each missing option:

- State the option name, type (`VALUE_NONE`, `VALUE_REQUIRED`, `VALUE_OPTIONAL`), and its description from the command's `configure()` method.
- Recommend whether it should be added to the Charter for Laravel UI, and if so, what field type to use (checkbox, select, text input, etc.), which file(s) to modify, and any conditional logic.
- For package options in particular, note whether the option is a **feature** (boolean toggle, shown as badges on the Package page) or **metadata** (free-text input, shown in the metadata accordion section).

### 6. Provide implementation recommendations

After the report, for each missing option, recommend how it *should* be implemented. Reference the specific files:

#### Application track (`build-application.sh`)
- `app/Enums/BuildOptions.php` — whether a new category or value is needed
- `app/Http/Requests/BuildShowRequest.php` — what validation rules to add
- `app/Http/Controllers/BuildController.php` — which flag(s) to append to `$options` and/or new template variables to pass
- `resources/stubs/build-application.sh` — any new template variable placeholders
- `resources/js/pages/Build/Index.vue` — what form fields to add and under what conditions
- `tests/Feature/BuildControllerTest.php` — what test cases to write

#### Package track (`build-package.sh`)
- `app/Enums/BuildOptions.php` — whether `AvailablePackageFeatures` needs a new value
- `app/Http/Requests/BuildPackageRequest.php` — what validation rules to add
- `app/Http/Controllers/BuildController.php` — which flag(s) to append to `$options` and/or new template variables to pass
- `resources/stubs/build-package.sh` — any new template variable placeholders
- `resources/js/pages/Build/Package.vue` — what form fields to add and under what conditions (feature badges vs metadata inputs)
- `resources/js/build/index.ts` — auto-generated build options
- `tests/Feature/BuildPackageControllerTest.php` — what test cases to write

### 7. Update current options status

Update the `## Current Options Status` tables below to reflect any changes. Do not update options marked as strictly ignored — those are intentionally excluded and should remain unchanged.

## Current Options Status (as of last update)

### `laravel new` (Laravel Installer)

| Option | Type | Handled in Charter |
|---|---|---|
| `name` | argument (REQUIRED) | Yes — `$name` |
| `--dev` | VALUE_NONE | NO - Ignored should not be implemented |
| `--git` | VALUE_NONE | NO - Ignored should not be implemented |
| `--branch` | VALUE_REQUIRED | NO - Ignored should not be implemented |
| `--github` | VALUE_OPTIONAL | NO - Ignored should not be implemented |
| `--organization` | VALUE_REQUIRED | NO - Ignored should not be implemented |
| `--database` | VALUE_REQUIRED | Yes — `$databaseFlag` |
| `--{react,svelte,vue,livewire}` | VALUE_NONE | Yes — `$frontendFlag` |
| `--livewire-class-components` | VALUE_NONE | Yes — `$frontendFlag` |
| `--workos` | VALUE_NONE | Yes — `$authFlag` |
| `--teams` | VALUE_NONE | Yes — `$teamsFlag` |
| `--no-authentication` | VALUE_NONE | Yes — `$authFlag` |
| `--{pest,phpunit}` | VALUE_NONE | Yes — `$testFramework` |
| `--{npm,pnpm,bun,yarn}` | VALUE_NONE | Yes — `$javascriptRuntime` |
| `--no-node` | VALUE_NONE | Yes — `$noNodeFlag` |
| `--{boost,no-boost}` | VALUE_NONE | Yes — `$boost` |
| `--using` | VALUE_OPTIONAL | Yes — `$usingFlag` |
| `--force`/`-f` | VALUE_NONE | NO - Ignored should not be implemented |

### `laravel package` (Laravel Installer)

| Option | Type | Handled in Charter |
|---|---|---|
| `name` | argument (OPTIONAL) | Yes — `$name` |
| `--force`/`-f` | VALUE_NONE | NO - Ignored should not be implemented |
| `--config` | VALUE_NONE | Yes — feature badge |
| `--routes` | VALUE_NONE | Yes — feature badge |
| `--views` | VALUE_NONE | Yes — feature badge |
| `--translations` | VALUE_NONE | Yes — feature badge |
| `--migrations` | VALUE_NONE | Yes — feature badge |
| `--assets` | VALUE_NONE | Yes — feature badge |
| `--commands` | VALUE_NONE | Yes — feature badge |
| `--facade` | VALUE_NONE | Yes — feature badge |
| `--boost-skill` | VALUE_NONE | Yes — feature badge |
| `--author-name` | VALUE_REQUIRED | Yes — metadata input |
| `--author-email` | VALUE_REQUIRED | Yes — metadata input |
| `--package-name` | VALUE_REQUIRED | Yes — metadata input |
| `--package-name-human` | VALUE_REQUIRED | Yes — metadata input |
| `--package-description` | VALUE_REQUIRED | Yes — metadata input |
| `--vendor-namespace` | VALUE_REQUIRED | Yes — metadata input |
| `--class-name` | VALUE_REQUIRED | Yes — metadata input |

### `sail:install` (Laravel Sail)

| Option | Handled in Charter |
|---|---|
| `--with` | Yes — `$with` |
| `--devcontainer` | Yes — `$devcontainer` |
| `--php` | Yes — `$php` |
