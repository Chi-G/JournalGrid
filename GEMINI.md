@~/.gemini/GEMINI.md

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application. Its frontend is 100% Livewire — there is no Inertia, no Vue, and no Filament in this repository. Do not introduce any of those, and do not reach for SPA/API patterns (Eloquent API Resources, `resources/js/Pages`) that belong to a different stack. Main ecosystem packages & versions:

- php - 8.3
- laravel/framework (LARAVEL) - v13
- livewire/livewire (LIVEWIRE) - v4
- laravel/fortify (FORTIFY) - v1
- unnathianalytics/laragrid (LARAGRID) - v1
- spatie/laravel-permission (PERMISSION) - v6
- spatie/laravel-activitylog (ACTIVITYLOG) - v4
- brick/money (MONEY) - v0
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isVoucherBalanced`, not `check()`.
- Check for existing components to reuse before writing a new one — in particular, check `app/Grids/` and `app/Livewire/` before adding a new grid or component that duplicates an existing pattern.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Business logic lives in `app/Actions/`, not in Livewire components, controllers, or models. See the "JournalGrid Architecture Rules" section at the bottom of this file before writing any voucher-related logic.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant (e.g. `packages: ['livewire', 'laragrid']`).
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `computed property`, not `livewire 4 computed property`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=vouchers`, `--path=journal`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show laragrid`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'JournalVoucher::where("status", "draft")->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public JournalVoucherRepository $vouchers) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isBalanced(JournalVoucher $voucher): bool`
- Use TitleCase for Enum keys: `Draft`, `Posted`, `Reversed`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic (e.g. balance-guard edge cases).
- Use array shape type definitions in PHPDoc blocks.
- Never use `float` or `decimal` casts for monetary values. Use `bigInteger` minor-unit columns or `brick/money\Money` value objects — see "Money Handling" below.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- This project's actual deployment target is a self-managed Hostinger KVM VPS (Nginx + Supervisor + Certbot), consistent with other Forahia Solutions projects. Do not assume Laravel Cloud/Forge-specific config files are in use unless told otherwise.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class that isn't an Action, use `php artisan make:class`. For Actions (see architecture rules below), a plain invokable class in `app/Actions/` is fine even without an Artisan generator.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.
- Models stay thin: relationships, casts, scopes, and accessors only. No business rules (balance checks, posting logic) inside model methods — those belong in Actions.

## Authentication

- This app uses `laravel/fortify` (headless) with fully custom Livewire components for login, registration, and two-factor challenge views — there is no Breeze/Jetstream Blade scaffolding to fall back on. Register any new Fortify view/action bindings in a dedicated service provider, not inline in `routes/web.php`.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model (e.g. a `posted()` state on the `JournalVoucherFactory`).
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Business-rule tests (balance enforcement, immutability, segregation of duties) should be unit tests against Actions, not feature tests through Livewire.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v13 structure rules ===

# Laravel 13 Structure

- Middleware are not registered in `app/Http/Kernel.php` (it doesn't exist). Configure them declaratively in `bootstrap/app.php` via `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application-specific service providers.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration; use `routes/console.php` for closures-based console/schedule definitions.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost. This matters especially for the `journal_lines`/`journal_vouchers` money columns — never regenerate a migration that drops the `bigInteger` minor-unit definition.
- Native query limiting on eager loads is available: `$query->latest()->limit(10);` — no external package needed.

### Models

- Casts should be set in a `casts()` method on the model rather than the `$casts` property. Follow existing conventions from other models in this codebase.

=== livewire/core rules ===

# Livewire 4

- This is the only frontend rendering layer in this app. Every page is a Livewire component (or a LaraGrid-powered Livewire component) — never introduce Blade `@livewire` directives where a full-page component would do, and never reach for a JS SPA pattern.
- Livewire components are UI orchestration only: they hold view state, call exactly one Action per user-triggered write, and translate the Action's result into a flash message / redirect / re-render. They must not contain balance checks, posting rules, or other business logic — see "JournalGrid Architecture Rules" below.
- Use `#[Computed]` properties for derived, cacheable-per-request view data (e.g. the current voucher's running total) instead of recalculating in the Blade view.
- Use `wire:model.live` sparingly — prefer deferred/`blur` binding for text inputs to avoid excessive round-trips, especially on grid header fields.
- Test every component with `Livewire::test()`; for grid-bearing components, drive interactions through the grid's public RPC methods (`gridOps`, etc.) rather than reaching into private component state.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- Balance-enforcement, immutability, and segregation-of-duties tests must assert on actual reconciled totals (`total_debit_minor === total_credit_minor`), not just on the absence of a validation error.

=== laragrid/core rules ===

# LaraGrid

- LaraGrid is the Excel-style Livewire datagrid this project is built to showcase. Consult its README/docs before inventing a pattern it already provides — do not build a custom Livewire repeater or Alpine table where a Grid definition would do the job.
- Three grid modes exist: Display (client-sorted, computed/aggregate data passed directly — used for the Trial Balance report), Readonly server-side (search/filter/paginate/export/saved views — used for voucher listing and per-account GL views), and Editable (used only for journal voucher line entry).
- Editable grids for voucher lines always declare `->rowsFrom()`, `->minRows(1)`, `->autoAppend()`, and `->completeWhenBalanced('debit', 'credit')` together.
- Every grid's `->authorize()` call must reference a Policy method — never a raw permission string or inline `$user->hasRole()` check duplicated from the Policy.
- `SearchSelectColumn` enrichment via `->onSelect()` belongs inside the Grid definition's closure, not in a model observer — keep the optimistic-update hook logic in one place.
- Do not use a readonly server-side grid for the Trial Balance report just because it seems more consistent with the other list pages — it's a computed aggregate, which is exactly what Display mode exists for.

=== permission/core rules ===

# spatie/laravel-permission

- Roles for this app: Accountant, Approver, Auditor, Admin. Permissions are granular (`voucher.create`, `voucher.post`, `voucher.reverse`, `voucher.view`, `chart-of-accounts.manage`) and assigned to roles via a seeder, not hardcoded checks scattered through the app.
- Never assign `voucher.create` and `voucher.post` to the same role by default — segregation of duties is a functional requirement of this app, not a suggestion.
- Use `$user->can(...)` / Policy methods in application code; reserve Blade `@can` directives for view-only conditionals, and even then prefer hiding UI via the Livewire component's own authorization check so unauthorized users never see the affordance render at all.

=== activitylog/core rules ===

# spatie/laravel-activitylog

- `JournalVoucher` and `JournalLine` use the `LogsActivity` trait. Log the voucher's full lifecycle (created, posted, reversed) — never disable logging on these models "to reduce noise."
- Do not write custom audit-log tables or columns duplicating what activitylog already provides.

=== money/core rules ===

# brick/money

- All monetary values that cross an Action boundary are `Brick\Money\Money` objects constructed from the `bigInteger` minor-unit column plus the ledger's currency (NGN by default). Convert at the model boundary (accessor/mutator or a dedicated cast), not ad hoc in controllers or Livewire components.
- Never perform arithmetic on monetary values using native PHP `+`/`-` on raw floats or unconverted integers outside of a `Money` object — this is the single most important rule in this codebase.

</laravel-boost-guidelines>

=== journalgrid/project rules ===

# JournalGrid — Project Domain Rules

JournalGrid is a double-entry journal voucher / general ledger entry system, built to demonstrate `unnathianalytics/laragrid` against the exact use case it was extracted from: a production accounting system for spreadsheet-trained operators. Every architectural decision here should read as "built by someone who understands both accounting discipline and modern Laravel conventions."

## Non-negotiable domain rules

1. **Money is never a float.** Monetary columns are `bigInteger` minor units (kobo) or `brick/money` value objects at the boundary. Never introduce a `decimal` cast on a money column without discussing it first.
2. **A journal voucher's lines must always net to zero** (`Σdebit = Σcredit`) before the voucher can move out of `draft`. This is enforced in THREE places: the LaraGrid `->completeWhenBalanced('debit', 'credit')` client guard, the Action's server-side re-validation, and a DB constraint or scheduled reconciliation job. Never remove one layer because another already covers it.
3. **Posted vouchers are immutable.** Never update a `journal_lines` row belonging to a `status = posted` voucher. The only valid operation is a reversal, which creates a NEW voucher (`reversal_of_id`) with mirrored debit/credit values. If asked to "edit a posted voucher," push back and suggest a reversal + new entry instead.
4. **Segregation of duties is a hard authorization rule.** The role that creates a voucher (Accountant) must not, by default, hold the permission to post it (Approver). Never collapse `voucher.create` and `voucher.post` into the same check "for simplicity."
5. **Voucher numbers are generated through `VoucherNumberGenerator`**, never the autoincrement `id`. Never expose the primary key as a user-facing/business identifier (URLs, exports, printed vouchers).

## Architecture conventions (SOLID, applied)

- **Actions own business logic.** One invokable class per operation: `CreateJournalVoucherAction`, `PostJournalVoucherAction`, `ReverseJournalVoucherAction`, `RecalculateTrialBalanceAction`. Livewire components call exactly one Action and translate its result into UI state.
- **Events for side effects, not more conditionals in the Action.** `VoucherPosted`, `VoucherReversed` dispatch listeners (`UpdateAccountBalances`, `NotifyApprover`, `WriteActivityLog`). New side effect = new listener, not an edit to the Action.
- **Constructor-inject collaborators.** Actions/services receive dependencies (query objects, `VoucherNumberGenerator`, `ExporterRegistry`) via the constructor — never `SomeModel::query()` inline inside an Action.
- **Policies are the single source of authorization truth.** Every grid's `->authorize()`, every controller, every queued job references the same Policy methods.
- **DB transactions wrap every multi-row write.** Voucher header + lines are one atomic unit — wrap creation, posting, and reversal in `DB::transaction()`.

## Testing expectations

- Every Action gets a Pest unit test that does NOT boot Livewire.
- Every grid gets a Pest feature test using `Livewire::test(...)->call('gridOps', ...)`.
- Never mark a posting/reversal test as passing without asserting the resulting totals reconcile to zero net change across the GL.