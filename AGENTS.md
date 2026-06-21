# AGENTS.md

## Business logic (summary)

App de gastos personales: solo registra **egresos**. Multi-usuario preparado
(uso propio), deployable, instalable como PWA.

**Entidades principales:**
- `categories` — default seedeadas (`is_default=true`, `user_id=null`, no se
  pueden eliminar) + custom del usuario (`user_id`).
- `expenses` — gasto real. Campos clave: `code` (id legible), `draft` (boolean,
  `true` = pendiente de confirmar), `type`, `expense_date`, `amount`. Casts:
  `amount` `decimal:4`, `expense_date` `datetime`, `draft`/`is_default` `bool`.
- `recurring_expenses` — plantilla (no es un gasto). Tiene
  `custom_interval_value` + `custom_interval_unit` y `next_due_date`. El cron
  genera `expenses` hijos con `type='recurring_child'`, `draft=true`. Permite
  pausar con `is_active=false`.
- `installment_groups` — agrupa cuotas. `total_amount`, `total_installments`.
  Cada cuota es un `expense` con `type='installment'` + `installment_number`.
- `expense_splits` — detalle informativo (no es sistema de deudas). La suma de
  splits ≤ `amount` del gasto.
- `shared_reports` — links públicos read-only vía token de 64 chars en
  `/share/{token}`. Tiene `label`, `filters` (JSON), `expires_at`.

**Tipos de expense:** `one_time` (puntual), `recurring_child` (generado por
cron), `installment` (cuota manual). El form muestra campos extra según el
tipo elegido.

**Lifecycle de draft (server-only, nunca exponer en forms):**
- Creación manual (usuario) → el `expense` se crea en el `submit` del form con `draft=false`. No hay placeholder previo en el form.
- Creación por cron (`recurring_child`) → el `expense` se crea con `draft=true` (pendiente de confirmar por el usuario).
- Confirmación de un draft → acción `approve()` en `BaseModel` setea `draft=false`.
- Queries de display → filtrar `draft=false` salvo que se trabaje con drafts.
- `draft=true` queda reservado exclusivamente para gastos generados por el cron. No se permite setearlo desde el form.

**Autorización:**
- `CategoryPolicy` — solo el dueño borra custom; las default nunca.
- `ExpensePolicy` — solo el dueño ve/edita/elimina/confirma sus gastos.
- Scope `byAuthor` en `BaseModel` filtra por `user_id` (crons, jobs, comandos).
- Acción `approve()` en `BaseModel` setea `draft=false`.

**Páginas autenticadas:** Dashboard, Gastos, Recurrentes, Cuotas, Categorías,
Reportes compartidos. **Pública:** `/share/{token}`.

**Filtros (`ExpenseFilter`):** `user_id` (server-only), `type`, `draft`,
`category_id`, `date_from`, `date_to`.

`BUSINESS.md` es la fuente de verdad detallada (dominio, flujos, edge cases).
Leerlo solo cuando se indique explícitamente o ante ambigüedad.

## Stack

- Laravel 13, PHP 8.3+, Livewire 4.2 (SFC mode), Vite 8
- Tailwind CSS **v3.4** (Breeze auth + profile views) — **not** v4
- Bootstrap 5.3, Bootstrap Icons, jQuery 4, DataTables 2.2.2, Select2 4.1, Flatpickr (all CDN-loaded)
- Alpine.js (bundled by Vite via `resources/js/app.js`)
- Axios configured in `resources/js/bootstrap.js` (Livewire handles its own AJAX; axios is not used today)
- PostgreSQL (dev), SQLite in-memory (tests)
- UUID primary keys on all models via `App\Traits\HasUuid` on `BaseModel`

## Commands

| Task | Command |
|------|---------|
| Dev server (app + queue + vite) | `composer dev` |
| Run all tests | `composer test` |
| Run single test | `./vendor/bin/phpunit tests/Feature/SomeTest.php` |
| Lint / fix PHP | `./vendor/bin/pint` |
| Build frontend | `npm run build` |
| Migrate | `php artisan migrate` |

## Architecture

- **Models** extend `BaseModel` (UUID keys, soft deletes, `byAuthor` scope, `approve` method).
- **Controllers** return JSON for API-style CRUD; views rendered for dashboard pages.
- **Form Requests** (`app/Http/Requests/`) handle HTTP validation; **`#[Validate]` attributes** are used for validation inside Livewire SFCs.
- **Policies** (`app/Policies/`) handle authorization (`$this->authorize('verb', $model)`).
- **Filters** (`app/Filters/`) encapsulate query filtering logic (see `ExpenseFilter`) — reusable from both controllers and Livewire components.
- **Two layout systems coexist** (see "Frontend ↔ Backend flow" below):
  - `resources/views/layouts/main.blade.php` — Bootstrap 5 dashboard (authenticated app).
  - `resources/views/layouts/app.blade.php` + `layouts/guest.blade.php` — Breeze/Tailwind for auth + profile screens.
- **Views**: Blade with `resources/views/pages/` (dashboard pages), `resources/views/components/` (reusable components + Livewire SFCs), `resources/views/layouts/`.
- **CSS**: Custom properties in `resources/css/app.css` (Bootstrap-style theme variables) + Tailwind utility classes (used by Breeze views).
- **Auth**: Fortify backend + Breeze views; `routes/auth.php` mounts all auth endpoints, `routes/web.php` mounts the app.

## Frontend ↔ Backend flow

This is the end-to-end guide for how a request becomes a page and how the page talks back to the server.

### 1. Request → response pipeline

1. Herd serves the app at `https://stardust-money.test`.
2. Laravel routes the request via `routes/web.php` (app) or `routes/auth.php` (auth).
3. A controller returns either a `View` (page render) or a `JsonResponse` (AJAX/JSON).
4. The Blade view extends a layout; the layout extends `resources/views/app.blade.php` (root skeleton: `<html>` + `<head>` + `<body>` + `@stack('styles')` + `@stack('scripts')`).
5. `app.blade.php` includes `resources/views/base/head.blade.php` and `base/scripts.blade.php`, which inject:
   - **CDN assets**: Bootstrap 5 CSS/JS, Bootstrap Icons, jQuery 4, Select2, DataTables, Flatpickr.
   - **Vite assets**: `resources/css/app.css` (Tailwind + custom CSS) and `resources/js/app.js` (Alpine bootstrap).
   - **Livewire**: `@livewireStyles` in head, `@livewireScripts` in body.

> Vite only bundles Tailwind + Alpine. All other JS/CSS libraries come from CDN, so `npm run build` is **not** required to ship jQuery/Bootstrap/DataTables changes — only Tailwind/Alpine changes.

### 2. Two layouts, two UI stacks — keep them separate

| Stack | Where | Look & feel |
|-------|-------|-------------|
| **Dashboard (Bootstrap 5 + jQuery + DataTables + Select2 + Flatpickr)** | `layouts/main.blade.php`, pages in `resources/views/pages/` | Bootstrap 5 components, DataTables, modals via `data-bs-toggle="modal"` |
| **Breeze auth + profile (Tailwind)** | `layouts/app.blade.php` + `layouts/guest.blade.php`, pages in `resources/views/auth/`, `resources/views/profile/` | Tailwind utility classes, `<x-*>` components (`<x-primary-button>`, `<x-modal>`, `<x-dropdown>`, etc.) |

Do not mix Breeze Tailwind components (e.g. `<x-primary-button>`, `<x-modal>`) on dashboard pages, and do not mix Bootstrap classes (`btn btn-accent`, `card-custom`) on Breeze views.

### 3. Three ways the front talks to the back

| Pattern | Used for | Mechanism |
|---------|----------|-----------|
| **HTTP form (Blade)** | Login, register, profile update, password change, logout | `<form method="POST" action="{{ route('...') }}">` + `@csrf`; redirects back with errors or `session('status')` |
| **jQuery / DataTables server-side (JSON)** | Expenses listing (and any future large data tables) | `serverSide: true, ajax: '<route>'`; consumes DataTables-shaped JSON; CSRF not needed (GET) |
| **Livewire SFC** | Modal forms with server-side state (e.g. expense creation) | SFC in `resources/views/components/?<name>.blade.php`; embedded via `<livewire:<name> />`; Livewire POSTs to `/livewire/update` |

### 4. Walkthrough — "Create an expense" (Livewire path)

1. User clicks **"Registrar gasto"** → `data-bs-toggle="modal" data-bs-target="#expenseModal"` opens a Bootstrap modal.
2. The modal body renders `<livewire:expense-form />` (SFC at `resources/views/components/?expense-form.blade.php`).
3. SFC's `mount()` seeds `expense_date` with today; the `#[Computed] categories()` property returns the user's categories via `Category::query()->byAuthor(Auth::id())`.
4. The form is wired with `wire:model` and `wire:submit="save"`. Submitting hits `/livewire/update` — no manual CSRF handling, no JSON.
5. The `save()` method runs `validate()` (uses `#[Validate]` attributes), then `Expense::create([...])`, then `dispatch('expense-created')`.
6. The inline script registered in `resources/views/components/expenses/form.blade.php` listens for `expense-created` and calls `bootstrap.Modal.getInstance(el).hide()`.
7. **The DataTable does not auto-refresh** — the page script in `pages/expenses/scripts.blade.php` does not call `table.draw()` on this event. Today the user must click **Filtrar** or reload to see the new row. (See "Known gaps" below.)

### 5. Walkthrough — "List expenses" (DataTables server-side path)

1. `GET /expenses` → `ExpenseController@index` → renders `pages/expenses/index.blade.php`.
2. The view `@push('scripts')` includes `pages/expenses/scripts.blade.php`.
3. That script:
   - On `$(document).ready`, populates `#filter-category` via `$.get('{{ route('categories.index') }}')`.
   - Initializes DataTables with `serverSide: true, ajax: '{{ route('expenses.data') }}'`, sending the current filter values from the form (category, type, draft, date_from, date_to).
4. Every DataTable request (page change, search, sort, filter button) hits `GET /expenses/data`.
5. `ExpenseController@data` builds a query with `Expense::datatable(ExpenseFilter::fromRequest($request))` and returns a DataTables-shaped JSON envelope via `DataTables::eloquent($query)->toJson()`. Columns are formatted in the controller (`amount` → `1.234,56`, `draft` → badge, `type` → human label, etc.).
6. The `actions` column is rendered server-side using the partial `components/expenses/actions.blade.php` (`@include` from the controller).

### 6. Where things live

| Concern | Location |
|---------|----------|
| Routes (app) | `routes/web.php` |
| Routes (auth) | `routes/auth.php` |
| Controllers | `app/Http/Controllers/` (returns `View` for pages, `JsonResponse` for AJAX) |
| Form Requests (HTTP) | `app/Http/Requests/` |
| Policies | `app/Policies/` (auto-resolved, call via `$this->authorize(...)`) |
| Filters | `app/Filters/` (reusable across controllers and Livewire) |
| Models | `app/Models/` (extend `BaseModel`) |
| Blade pages | `resources/views/pages/` |
| Blade layouts | `resources/views/layouts/` |
| Anonymous Blade components | `resources/views/components/` (e.g. `common/button`, `expenses/form`, layout pieces) |
| Livewire SFCs | `resources/views/components/?<name>.blade.php` (the `?` prefix is Livewire's lazy-load convention) |
| Livewire config | `config/livewire.php` — `component_locations` = `resources/views/components` + `resources/views/livewire`; class-based components would live in `app/Livewire/` (none exist yet) |
| Page-specific JS | `resources/views/pages/<page>/scripts.blade.php` pushed via `@push('scripts')` |
| Global jQuery plugins | `resources/js/main.js` (Select2 + Flatpickr init) |
| Alpine bootstrap | `resources/js/app.js` |
| Axios (configured, not actively used) | `resources/js/bootstrap.js` |

### 7. How to add a new screen

1. Add a route in `routes/web.php` (always use named routes — referenced everywhere via `route(...)`).
2. Create the controller method that returns a `View`.
3. Create the view in `resources/views/pages/<feature>/index.blade.php` extending `layouts/main.blade.php`.
4. Put page-specific JS in `resources/views/pages/<feature>/scripts.blade.php` and `@push('scripts')` it from the page.
5. For forms with server-side state, create a Livewire SFC at `resources/views/components/?<name>.blade.php` and embed it with `<livewire:<name> />`. Reference it in tests as `Livewire::test('<name>')`.

### 8. How to add a new API endpoint consumed by jQuery/DataTables

1. Add the route in `routes/web.php` (`->name('foo.data')` for DataTables, `->name('foo.confirm')` for custom verbs).
2. Type-hint a `FormRequest` for validation; gate with a `Policy` via `$this->authorize(...)`.
3. Use an existing `Filter` (e.g. `ExpenseFilter::fromRequest($request)`) or create a new one under `app/Filters/`.
4. Return a `JsonResponse` — either raw JSON or via `DataTables::eloquent($query)->toJson()` for table feeds.
5. The DataTable `columns` array in the page script maps to either model attributes or formatted columns defined in the controller (`editColumn`, `addColumn`, `rawColumns`).

### 9. Patterns to keep

- **Validation**: `FormRequest` for HTTP endpoints, `#[Validate]` attributes for Livewire SFCs.
- **Authorization**: `$this->authorize(...)` in controllers; for Livewire, scope queries with `->byAuthor(Auth::id())` (the `AuthorizesRequests` trait is not used in the SFC yet).
- **Filters**: keep query logic in `App\Filters\*` so the same filter can be reused from controllers and Livewire.
- **Models**: extend `BaseModel`; use `byAuthor(Auth::id())` to scope queries to the current user.
- **Naming**: routes use dotted notation (`expenses.data`, `expenses.confirm`); Livewire SFCs use kebab-case (`expense-form`).
- **CSRF**: emitted via `<meta name="csrf-token">` in `app.blade.php`; Livewire handles it automatically; Breeze forms include `@csrf`; jQuery AJAX (if added later) must set the `X-CSRF-TOKEN` header.

### 10. Known gaps (be aware)

- After creating an expense via the modal, the expenses DataTable is **not** auto-refreshed. To fix: extend the `expense-created` listener in `components/expenses/form.blade.php` to also call the DataTable's `draw()` (the table instance is currently scoped inside `$(document)`, so it needs to be hoisted or re-discovered).
- The Auth controllers in `app/Http/Controllers/Auth/` (login, register, password reset, etc.) are still Breeze's defaults — no `App\Actions\Fortify` overrides yet. The login view is `resources/views/auth/login.blade.php`.
- `CategoryController::index` returns `name` and `id` but the page script in `pages/expenses/scripts.blade.php` reads both `cat.name` and `cat.description` (line 45) — the JSON only ships `name`, so the dropdown label is sourced from `name` only.
- `resources/js/bootstrap.js` exposes `window.axios` but nothing in the codebase uses it for HTTP requests today (Livewire handles its own AJAX).

## Conventions

- All domain models use `SoftDeletes`.
- Auth middleware **is active** in `routes/web.php` — the `Route::middleware('auth')->group(...)` block at line 12 wraps categories, expenses, and profile routes; the `dashboard` route additionally requires `verified`.
- Expense amounts cast as `decimal:4`; `expense_date` is a `datetime`; `draft` and `is_default` are `boolean`.
- **Tests follow the rules in the "Database testing rules" section below** — never use `RefreshDatabase`/`DatabaseMigrations`/`DatabaseTruncation`/`migrate:fresh`; create only the records a test needs and clean them up, or reuse existing records without modifying them.
- Livewire SFC filename starts with `?` (lazy-load); the same kebab-case name is used in `<livewire:<name> />` and `Livewire::test('<name>')`.

## Database testing rules

- NEVER use RefreshDatabase.
- NEVER use DatabaseMigrations.
- NEVER use DatabaseTruncation.
- NEVER call migrate:fresh from tests.
- NEVER reset, truncate, recreate or drop tables.

Tests run against a persistent development database.

When test data is needed:
- Create only the records required by the test.
- Clean up only records created by that test if necessary.
- Prefer unique identifiers to avoid collisions.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.28
- laravel/fortify (FORTIFY) - v1.37.2
- laravel/framework (LARAVEL) - v13.16.1
- laravel/prompts (PROMPTS) - v0.3.18
- livewire/livewire (LIVEWIRE) - v4.3.1
- laravel/boost (BOOST) - v2.4.10
- laravel/breeze (BREEZE) - v2.4.2
- laravel/mcp (MCP) - v0.8.1
- laravel/pail (PAIL) - v1.2.7
- laravel/pint (PINT) - v1.29.3
- phpunit/phpunit (PHPUNIT) - v12.5.30
- alpinejs (ALPINEJS) - v3.15.12
- tailwindcss (TAILWINDCSS) - v3.4.19

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
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
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- After modifying routes, config files, or Blade views, clear and rebuild the application cache by running `php artisan optimize:clear && php artisan optimize`

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, execute PHP and Composer commands, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`, `herd php artisan optimize`, `herd composer install`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>

## Draft Records
 
`draft` is a server-managed boolean on `expenses` (and any other model with a `draft` column). It indicates whether the record has been confirmed by the user.
 
Semantics:
- `draft = false` → confirmed expense. Created by the user submitting a form, or by `BaseModel::approve()` confirming a pending record.
- `draft = true` → pending confirmation. Reserved for expenses generated by the cron (`recurring_child`); the user reviews and confirms them later.
- `draft` is never sent from the client and never exposed via form inputs. Forms create the record at submit time with `draft = false`.
 
Rules:
- `draft` is a server-only field. Never expose it in forms, inputs, or frontend logic.
- On form submit: create the record directly with `draft = false`.
- When a cron generates an expense, create it with `draft = true`.
- To confirm a pending record, call `BaseModel::approve()`, which sets `draft = false`.
- When querying records for display, always filter by `draft = false` unless explicitly working with drafts.
