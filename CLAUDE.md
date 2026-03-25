# CLAUDE.md — AI Agent Guide for Cooperative Bank

This file gives AI coding agents (Claude Code, Cursor, Copilot) the context needed to work effectively in this codebase.

---

## Project Overview

Laravel 13 cooperative banking system. Multi-role web app + REST API.
Source lives at the repository root. Dockerized for standalone deployment.

---

## Commands

```bash
# Setup from scratch
composer install && npm install && cp .env.example .env
php artisan key:generate && php artisan migrate --seed

# Dev server (server + queue + logs + vite hot-reload)
npm run dev

# Individual services
php artisan serve           # web server on :8000
php artisan queue:work      # process queued jobs
php artisan schedule:work   # run scheduler locally

# Code quality
./vendor/bin/pint           # fix code style (Laravel Pint)
php artisan test            # run test suite (109 feature tests)

# Docker
docker compose up -d              # start (standalone, no PHP/Node needed)
docker compose up -d --build      # rebuild after code changes
docker compose down -v            # stop and reset all data
docker compose exec app sh        # shell into container
docker compose exec app php artisan test   # run tests in Docker

# Database
php artisan migrate         # run pending migrations
php artisan migrate:fresh --seed   # wipe and reseed
php artisan db:seed --class=RoleSeeder   # seed specific seeder

# Useful Tinker one-liners
php artisan tinker
>>> \App\Models\User::first()
>>> \App\Models\Customer::count()
```

---

## Architecture

### Layer Responsibilities

| Layer | Location | Purpose |
|---|---|---|
| Routes | `routes/web.php`, `routes/api.php` | URL to controller mapping |
| Controllers | `app/Http/Controllers/` | HTTP request/response only, no business logic |
| Form Requests | `app/Http/Requests/` | Input validation |
| Services | `app/Services/` | All business logic lives here |
| Models | `app/Models/` | Eloquent ORM, relationships, scopes |
| Events | `app/Events/` | Domain event data bags |
| Listeners | `app/Listeners/` | React to events (send notifications, log, etc.) |
| Jobs | `app/Jobs/` | Async heavy work dispatched to queue |

### Rule: Keep Controllers Thin

Controllers must only:
1. Validate input (delegate to Form Request)
2. Call one Service method
3. Return a response

Never put database queries, business rules, or notification logic directly in a controller.

### Service Method Pattern

```php
// Good: controller calls service
public function store(StoreCustomerRequest $request)
{
    $customer = $this->customerService->register($request->validated());
    return redirect()->route('clerk.customers.index');
}

// Bad: controller doing business logic
public function store(Request $request)
{
    $customer = Customer::create([...]);
    event(new CustomerRegistered($customer));
    Notification::send(...);
    // etc.
}
```

---

## Roles & Middleware

Roles: `SuperAdmin`, `Manager`, `Clerk`, `Cashier`, `Accountant`

Middleware registered in `bootstrap/app.php`:
- `role` → `RoleMiddleware` — checks `auth()->user()->role->name`
- `branch` → `SetBranchContext` — sets branch from session/user

Route groups use: `->middleware(['auth', 'role:SuperAdmin'])`

A higher-privilege role can access lower-privilege routes:
- Manager can also access Clerk and Cashier routes
- SuperAdmin can access everything

---

## Database Conventions

- All tables use `id` (auto-increment bigint) primary key
- Timestamps: `created_at`, `updated_at` on every table
- Soft deletes: use `deleted_at` where data must be retained
- Foreign keys: `{model}_id` convention (e.g. `customer_id`, `branch_id`)
- Money fields: store as `decimal(15,2)` — never float
- Status fields: use string enums (`pending`, `approved`, `rejected`, `active`, `closed`)

### Key Relationships

```
User → Role (belongs to)
User → Branch (belongs to)
Customer → User (created by clerk)
Customer → BankAccount[] (has many)
Customer → FdAccount[] (has many)
Customer → LoanApplication[] (has many)
LoanApplication → Loan (has one, after approval)
Loan → LoanTransaction[] (has many repayments)
BankAccount → BankAccountTransaction[] (has many)
FdAccount → FdTransaction[] (has many)
```

---

## Events & Listeners Map

| Event | Listeners |
|---|---|
| `CustomerRegistered` | `LogAuditTrail`, `SendCustomerWelcomeNotification` |
| `CustomerApproved` | `LogAuditTrail`, `SendCustomerApprovalNotification` |
| `AccountOpened` | `LogAuditTrail` |
| `LoanApplicationSubmitted` | `LogAuditTrail` |
| `LoanApproved` | `LogAuditTrail`, `SendLoanApprovalNotification` |
| `LoanDisbursed` | `LogAuditTrail`, `GenerateLoanInstallmentSchedule` |
| `LoanRepaymentRecorded` | `LogAuditTrail`, `SendRepaymentReceipt` |
| `FdAccountOpened` | `LogAuditTrail`, `SendFdOpeningConfirmation` |
| `FdMatured` | `LogAuditTrail`, `SendFdMaturityAlert` |
| `TransactionCompleted` | `LogAuditTrail` |
| `ShareTransactionCompleted` | `LogAuditTrail` |

All listeners are queued (implement `ShouldQueue`). Notification listeners dispatch `SendEmailJob` or `SendSmsJob`.

---

## Adding New Features

### New Route + Controller

1. Add route to appropriate role group in `routes/web.php`
2. Create controller in correct namespace (`app/Http/Controllers/{Role}/`)
3. Create Form Request in `app/Http/Requests/` if needed
4. Add business logic to existing or new Service in `app/Services/`
5. Create or reuse Blade views in `resources/views/{role}/`

### New Migration

```bash
php artisan make:migration create_{table}_table
# or
php artisan make:migration add_{column}_to_{table}_table
```

Always add foreign key constraints with `->constrained()->cascadeOnDelete()` or `->nullOnDelete()`.

### New Event

```bash
php artisan make:event MyNewEvent
php artisan make:listener HandleMyNewEvent --event=MyNewEvent
```

Register in `app/Providers/EventServiceProvider.php` `$listen` array.

---

## Common Pitfalls

- **Never store money as float.** Use `decimal(15,2)` in migrations and `string` cast in model (or a Money value object).
- **Always use Form Requests** — never validate in controllers with `$request->validate()`.
- **Queue workers must be restarted** after pulling new code: `php artisan queue:restart`.
- **Branch context** — many queries are scoped to `auth()->user()->branch_id`. If queries return empty, check the branch middleware is applied.
- **Sanctum CSRF** — SPA/web routes require `X-XSRF-TOKEN` header. API token routes use `Authorization: Bearer {token}`.

---

## Environment Variables to Know

```env
DB_CONNECTION=sqlite              # switch to mysql/pgsql for prod
DB_DATABASE=/absolute/path/to/database.sqlite

QUEUE_CONNECTION=database         # use redis for prod
MAIL_MAILER=log                   # change to smtp for prod
SMS_DRIVER=log                    # implement real driver for prod

BRANCH_ID=                        # can override branch context in dev
```

---

## File Size Limits (KYC uploads)

PAN and Aadhaar file uploads are stored as base64 in the database (`photo`, `id_proof1`, `id_proof2` columns on `customers`).
Max size: 2MB. Allowed types: `jpg`, `jpeg`, `png`, `pdf`.

---

## Testing

Two test suites: **PHPUnit** (fast backend logic) and **Laravel Dusk** (real browser UI).

### PHPUnit (Feature Tests)

Uses **SQLite in-memory** (`phpunit.xml` sets `DB_DATABASE=:memory:`). 109 tests, ~3s.

```bash
php artisan test                          # all tests
php artisan test --filter=BranchTest      # single file
php artisan test --testsuite=Feature      # feature tests only
```

Test files in `tests/Feature/` organized by role:

| Directory | Covers |
|---|---|
| `Auth/` | Login, logout, role redirects |
| `RoleAccess/` | All 5 roles × all route groups |
| `SuperAdmin/` | Branch, User, LoanType, FdSetup, AccountType, CompanySetup CRUD |
| `Manager/` | Customer approval/rejection, dashboard, workflows |
| `Clerk/` | Customer registration, loan application submission |
| `Cashier/` | Transaction deposit/withdraw, validation |
| `Accountant/` | Dashboard, role enforcement |

Every test uses `RefreshDatabase` trait and seeds roles via `RoleSeeder`. Create test data directly with `Model::create()` — no factories exist yet.

### Laravel Dusk (Browser Tests)

Uses **headless Chrome** via ChromeDriver. 6 sequential flow tests covering every page in the app. Produces **68 screenshots** in `tests/Browser/screenshots/` organized by role.

```bash
php artisan dusk                              # run all (headless)
php artisan dusk --browse                     # visible browser
php artisan dusk --filter=T02_SuperAdminFlow  # single role
```

Key files:
- `tests/DuskTestCase.php` — base class, auto-starts PHP server + ChromeDriver, migrates once
- `.env.dusk.local` — separate env with its own SQLite database (`database/dusk.sqlite`)
- `tests/Browser/T01_LoginFlowTest.php` through `T06_AccountantFlowTest.php` — one file per role

How Dusk works:
1. `php artisan dusk` swaps `.env` → `.env.dusk.local` (restores after)
2. `DuskTestCase::prepare()` starts ChromeDriver + a PHP dev server on port 8001
3. Each test class runs one `test_full_*_flow()` method that logs in, visits all pages, screenshots, logs out
4. Screenshots saved to `tests/Browser/screenshots/{01-auth,02-superadmin,...}/`

To add a new browser test for a page:
1. Find the role's flow test (e.g., `T03_ManagerFlowTest.php`)
2. Add `$browser->visit('/manager/new-page')->waitForText('Expected')->screenshot('03-manager/new-page');`
3. Run `php artisan dusk --filter=T03_ManagerFlow` to verify

---

## Docker

The app runs as a standalone Docker container (Nginx + PHP-FPM 8.4 + Supervisor + SQLite).

Key files:
- `Dockerfile` — multi-stage build (Node for assets → Composer for deps → PHP-FPM Alpine)
- `docker-compose.yml` — production config
- `docker-compose.override.yml` — dev overrides (mounts local source for live reload)
- `docker/` — nginx.conf, php.ini, supervisord.conf, entrypoint.sh
- `.dockerignore` — keeps image lean

The `entrypoint.sh` auto-creates `.env`, generates app key, runs migrations, and seeds on first boot. Caches are skipped in `APP_ENV=local`.
