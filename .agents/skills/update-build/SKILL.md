---
name: update-build
description: "Updates the resources/stubs/build.sh Blade template to stay in sync with upstream changes from laravel/installer and laravel/sail. Run this when new options or arguments are added to the `laravel new` or `sail:install` commands, or when updating those packages."
license: MIT
metadata:
  author: wilsenhc
---

# Update Build Stubs

## Workflow

### 1. Upgrade packages

```bash
vendor/bin/sail composer upgrade laravel/installer laravel/sail
```

### 2. Check if versions changed

Read `composer.lock` entries for `laravel/installer` and `laravel/sail` to confirm the `version` (for installer) or `source.reference` (for sail) changed. If neither package was upgraded, stop here.

### 3. Read vendor command files

Read the following files to identify all current options and arguments:

- `vendor/laravel/installer/src/NewCommand.php` — specifically `configure()` method (defines `addArgument()` and `addOption()` calls for `laravel new`)
- `vendor/laravel/sail/src/Console/InstallCommand.php` — specifically `$signature` property (defines options for `sail:install`)

### 4. Compare against build.sh

Read `resources/stubs/build.sh` and `app/Http/Controllers/BuildController.php` (the `show()` method that builds `$options` and passes variables to the Blade render).

Compare every option/argument from the vendor commands against what `BuildController::show()` passes in `$options` and what `build.sh` uses as template variables.

### 5. Report new options

ONLY list options/arguments that exist in the vendor commands but are NOT yet handled in `BuildController::show()` or `build.sh`. For each missing option:

- State the option name, type (`VALUE_NONE`, `VALUE_REQUIRED`, `VALUE_OPTIONAL`), and its description from the command's `configure()` method.
- Recommend whether it should be added to the Charter for Laravel form, and if so, what field type to use (checkbox, select, text input, etc.), which file(s) to modify (BuildController, BuildShowRequest, BuildOptions enum, frontend Vue component, etc.), and any conditional logic (e.g., only show when another option is selected).

### 6. Provide implementation recommendations

After the report, for each missing option, recommend how it *should* be implemented across the application. Do not implement anything — only list which files need changes and what those changes would look like. Reference the specific files:

- `app/Enums/BuildOptions.php` — whether a new category or value is needed
- `app/Http/Requests/BuildShowRequest.php` — what validation rules to add
- `app/Http/Controllers/BuildController.php` — which flag(s) to append to `$options` and/or new template variables to pass
- `resources/stubs/build.sh` — any new template variable placeholders
- `resources/js/pages/Build/Index.vue` — what form fields to add and under what conditions
- `tests/Feature/BuildControllerTest.php` — what test cases to write

### 7. Update current options status

Update the `## Current Options Status` table to reflect any changes. Do not update options marked as strictly ignored — those are intentionally excluded and should remain unchanged.

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
| `--database` | VALUE_REQUIRED | Partial (only auto-derived from services) |
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

### `sail:install` (Laravel Sail)

| Option | Handled in Charter |
|---|---|
| `--with` | Yes — `$with` |
| `--devcontainer` | Yes — `$devcontainer` |
| `--php` | Yes — `$php` |
