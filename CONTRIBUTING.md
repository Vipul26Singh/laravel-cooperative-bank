# Contributing to Cooperative Bank

Thank you for contributing! This guide covers branching strategy, code standards, and the PR process.

---

## Table of Contents

- [Getting Started](#getting-started)
- [Branching Strategy](#branching-strategy)
- [Coding Standards](#coding-standards)
- [Commit Messages](#commit-messages)
- [Pull Request Process](#pull-request-process)
- [Testing Requirements](#testing-requirements)
- [Database Changes](#database-changes)
- [Security](#security)

---

## Getting Started

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR_USERNAME/laravel-cooperative-bank.git
   cd laravel-cooperative-bank
   ```
3. Set up the project:
   ```bash
   composer install && npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```
4. Create a feature branch (see below)

---

## Branching Strategy

| Branch | Purpose |
|---|---|
| `main` | Production-ready code. Protected. No direct pushes. |
| `develop` | Integration branch. All PRs merge here first. |
| `feature/{short-description}` | New features |
| `fix/{short-description}` | Bug fixes |
| `hotfix/{short-description}` | Urgent production fixes (branch from `main`) |

### Branch Naming Examples

```
feature/gold-loan-valuation
feature/sms-otp-login
fix/fd-maturity-date-calculation
fix/cashier-balance-display
hotfix/login-redirect-loop
```

### Workflow

```bash
# Start from develop
git checkout develop
git pull origin develop

# Create your branch
git checkout -b feature/your-feature

# Work, commit, push
git push -u origin feature/your-feature

# Open PR against develop
```

---

## Coding Standards

This project uses **Laravel Pint** (PSR-12 based). Run before every commit:

```bash
./vendor/bin/pint
```

### PHP Rules

- PHP 8.4+ — use typed properties, constructor promotion, readonly where appropriate
- No raw SQL — use Eloquent or the Query Builder
- No business logic in controllers — delegate to Services
- No `dd()`, `var_dump()`, or debug output in committed code
- Use Form Request classes for all input validation
- Use `$fillable` (not `$guarded = []`) on models
- Always use database transactions for multi-step writes:
  ```php
  DB::transaction(function () {
      // ...
  });
  ```

### Blade / Frontend Rules

- Extend `layouts.app` for all authenticated views
- Use `@auth`, `@role` directives for access control in views
- Always escape output with `{{ }}` — only use `{!! !!}` for trusted HTML
- No inline styles — use Tailwind utility classes
- JavaScript: prefer vanilla JS or Alpine.js; avoid jQuery unless using an existing AdminLTE component

### Naming Conventions

| Thing | Convention | Example |
|---|---|---|
| Controller | PascalCase + Controller | `LoanApplicationController` |
| Model | PascalCase singular | `LoanApplication` |
| Migration | snake_case verb_noun | `create_loan_applications_table` |
| Event | PascalCase past tense | `LoanApplicationSubmitted` |
| Listener | PascalCase present | `GenerateLoanInstallmentSchedule` |
| Job | PascalCase verb | `ProcessFdMaturityJob` |
| Route name | dot-notation | `clerk.loan-applications.create` |
| Blade file | kebab-case | `loan-applications/create.blade.php` |

---

## Commit Messages

Follow the **Conventional Commits** spec:

```
<type>(<scope>): <short summary>

[optional body]
```

**Types:**
- `feat` — new feature
- `fix` — bug fix
- `refactor` — code change that neither fixes a bug nor adds a feature
- `test` — adding or updating tests
- `docs` — documentation only
- `chore` — build process, dependency updates, etc.
- `migration` — database schema changes

**Examples:**

```
feat(loans): add gold loan valuation step in application flow
fix(cashier): correct balance deduction on withdrawal
refactor(services): extract interest calculation into LoanService
test(customers): add feature test for customer approval workflow
migration: add guarantor_id to loans table
docs: update API reference for FD endpoints
```

---

## Pull Request Process

1. **Self-review** — read your own diff before opening the PR
2. **Fill in the PR template** — title, summary, test plan
3. **Link the issue** — `Closes #123` in the description
4. **Pass all checks:**
   - `./vendor/bin/pint --test` (no style violations)
   - `php artisan test` (all tests pass)
5. **Request review** — at least one approval required
6. **Squash and merge** into `develop`

### PR Title Format

```
feat(scope): brief description   ← for features
fix(scope): brief description    ← for fixes
```

Keep it under 72 characters.

---

## Testing Requirements

Every PR should include tests proportional to the change:

| Change type | Required tests |
|---|---|
| New Service method | Unit test for the method |
| New Controller action | Feature test (HTTP assertion) |
| New Event/Listener | Feature test triggering the event |
| Bug fix | Regression test that fails before the fix |
| New migration | No test required, but seed data if needed |

### Writing Tests

```bash
# Create a feature test
php artisan make:test Loans/LoanApprovalTest

# Create a unit test
php artisan make:test Services/LoanServiceTest --unit

# Run tests inside Docker
docker compose exec app php artisan test
```

Use `RefreshDatabase` trait and seed roles in `setUp()`. No factories exist yet — create test data with `Model::create()`.

```php
use App\Models\{User, Role, Branch, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $branch = Branch::create(['name' => 'Main', 'code' => 'BR001', 'address' => 'Addr', 'is_active' => true]);
        $role = Role::where('name', 'Manager')->first();
        $this->manager = User::create([
            'name' => 'Manager', 'email' => 'mgr@test.com',
            'password' => bcrypt('Password@123'), 'role_id' => $role->id,
            'branch_id' => $branch->id, 'is_active' => true,
        ]);
    }

    public function test_manager_can_approve_customer(): void
    {
        $customer = Customer::create([...]);

        $this->actingAs($this->manager)
            ->post("/manager/customers/{$customer->id}/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id, 'approval_status' => 'approved',
        ]);
    }
}
```

---

## Database Changes

### Adding a Migration

```bash
php artisan make:migration add_guarantor_to_loans_table
```

Rules:
- Always add `down()` method to reverse the migration
- Never modify an existing migration that has been pushed — create a new one
- Use `->nullable()` for optional columns added to existing tables
- Add indexes for any column used in `WHERE` clauses
- Use `decimal(15,2)` for all monetary amounts

### Seeders

If your feature requires reference data, add a seeder:

```bash
php artisan make:seeder YourFeatureSeeder
```

Register it in `DatabaseSeeder::run()`. Seeders must be **idempotent** — use `firstOrCreate` or `insertOrIgnore`.

---

## Security

- **Never commit secrets** — no API keys, passwords, or tokens in code
- **No `eval()`, `exec()`, `shell_exec()`** unless absolutely necessary and reviewed
- **Validate and sanitize all user input** using Form Requests
- **Use parameterized queries** — Eloquent handles this; avoid raw SQL with user input
- **File uploads** — validate mime type server-side, store outside `public/`, use randomized filenames
- **Report vulnerabilities privately** by opening a GitHub Security Advisory, not a public issue

---

## Questions?

Open a GitHub Discussion or an issue with the `question` label.
