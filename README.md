# Cooperative Bank Management System

A full-featured cooperative banking management system built with **Laravel 13**, supporting multi-role operations, loan management, fixed deposits, share accounts, and real-time transaction processing.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Quick Start](#quick-start)
- [Docker (Standalone)](#docker-standalone)
- [User Roles](#user-roles)
- [Architecture Overview](#architecture-overview)
- [API Reference](#api-reference)
- [Scheduled Tasks](#scheduled-tasks)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Multi-Role Access Control** — SuperAdmin, Manager, Clerk, Cashier, Accountant
- **Customer Management** — Registration, KYC (PAN + Aadhaar), approval workflow
- **Bank Accounts** — Savings, Current, OD account types with auto-generated account numbers
- **Fixed Deposits (FD)** — FD account creation, maturity tracking, auto-renewal
- **Loans** — Application workflow, disbursement, EMI schedule generation, repayment tracking
- **Gold Loans** — Dedicated gold loan product support
- **Share Accounts** — Member share management and transactions
- **Transactions** — Deposits, withdrawals, transfers, with full audit trail
- **Reports** — Loan outstanding, transaction statements, loan demand collection sheets
- **Event-Driven Notifications** — Email and SMS on key actions (customer approval, loan disbursement, FD maturity, etc.)
- **REST API** — Full Sanctum-authenticated API for mobile/SPA clients
- **Audit Logging** — Every sensitive action is logged with user, branch, and timestamp
- **Multi-Branch Support** — Branch context middleware for isolated operations

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4+, Laravel 13 |
| Database | SQLite (dev) / MySQL or PostgreSQL (prod) |
| Authentication | Laravel Sanctum |
| Queue | Database queue driver |
| Frontend | Blade + AdminLTE 3, Tailwind CSS 4, Vite 8 |
| API Client (JS) | Axios |
| Testing | PHPUnit 12 |
| Code Style | Laravel Pint |
| Containerization | Docker + Docker Compose |

---

## System Requirements

- PHP >= 8.4
- Composer >= 2.x
- Node.js >= 20.x & npm >= 10.x
- SQLite 3 (dev) or MySQL 8+ / PostgreSQL 14+ (prod)

---

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/vipul26singh/laravel-cooperative-bank.git
cd laravel-cooperative-bank

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run migrations and seed initial data
php artisan migrate --seed

# 7. Build frontend assets
npm run build

# 8. Start the development server
php artisan serve
```

The app will be available at `http://localhost:8000`.

**Default admin credentials:**
- Email: `admin@coopbank.com`
- Password: `Admin@123`

### Development Mode (all services at once)

```bash
npm run dev
```

This concurrently starts the PHP dev server, queue worker, log watcher, and Vite hot-reload server.

---

## Docker (Standalone)

Run the entire application as a self-contained Docker container — no PHP, Composer, or Node.js required on the host.

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) >= 20.10
- [Docker Compose](https://docs.docker.com/compose/install/) >= 2.x

### One-Command Start

```bash
docker compose up -d
```

The app will be available at `http://localhost:8000`.

On first launch the container automatically:
1. Generates an application key
2. Creates the SQLite database
3. Runs all migrations
4. Seeds default data (roles, admin user, etc.)

**Default admin credentials:**
- Email: `admin@coopbank.com`
- Password: `Admin@123`

### Common Operations

```bash
# View logs
docker compose logs -f app

# Stop the app
docker compose down

# Stop and remove all data (database + uploads)
docker compose down -v

# Rebuild after code changes
docker compose up -d --build

# Run artisan commands inside the container
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate:status

# Access a shell inside the container
docker compose exec app sh
```

### Configuration

Override settings via environment variables in `docker-compose.yml` or a `.env` file:

| Variable | Default | Description |
|---|---|---|
| `APP_PORT` | `8000` | Host port to expose |
| `APP_ENV` | `production` | Application environment |
| `APP_DEBUG` | `false` | Debug mode |
| `APP_URL` | `http://localhost:8000` | Application URL |
| `MAIL_MAILER` | `log` | Mail driver (`log`, `smtp`) |
| `MAIL_HOST` | — | SMTP host |
| `MAIL_PORT` | — | SMTP port |
| `MAIL_USERNAME` | — | SMTP username |
| `MAIL_PASSWORD` | — | SMTP password |

### Data Persistence

Two named Docker volumes are used:
- `db_data` — SQLite database file
- `storage_data` — uploaded files (KYC documents, etc.)

Data survives container restarts and rebuilds. To fully reset, run `docker compose down -v`.

### Architecture

The container bundles everything in a single image:
- **Nginx** — web server on port 80
- **PHP-FPM 8.4** — application runtime
- **Supervisor** — manages Nginx, PHP-FPM, queue worker, and scheduler
- **SQLite** — embedded database (no external DB needed)

```
┌──────────────────────────────────────────┐
│            Docker Container              │
│                                          │
│   Nginx :80  ──►  PHP-FPM 8.4 :9000     │
│                                          │
│   Supervisor                             │
│   ├── php-fpm                            │
│   ├── nginx                              │
│   ├── queue:work (background jobs)       │
│   └── schedule:run (cron loop)           │
│                                          │
│   SQLite (database/database.sqlite)      │
└──────────────────────────────────────────┘
```

---

## User Roles

| Role | Access Level | Primary Responsibilities |
|---|---|---|
| **SuperAdmin** | Full system | Branches, users, loan types, FD setup, account types, company config |
| **Manager** | Branch-level | Approve customers/loans, open accounts, oversee operations |
| **Clerk** | Data entry | Register customers, submit loan applications |
| **Cashier** | Transactions | Process deposits, withdrawals, loan repayments |
| **Accountant** | Read + reports | View reports, loan outstanding, transaction statements |

---

## Architecture Overview

```
app/
├── Console/Commands/       # Scheduled CLI commands
├── Events/                 # Domain events (11 events)
├── Http/
│   ├── Controllers/
│   │   ├── Api/            # REST API controllers
│   │   ├── SuperAdmin/     # SuperAdmin web controllers
│   │   ├── Manager/        # Manager web controllers
│   │   ├── Clerk/          # Clerk web controllers
│   │   ├── Cashier/        # Cashier web controllers
│   │   └── Accountant/     # Accountant web controllers
│   ├── Middleware/         # RoleMiddleware, SetBranchContext
│   └── Requests/           # Form request validation (7 classes)
├── Jobs/                   # Queued jobs (FD maturity, OD interest, email, SMS)
├── Listeners/              # Event listeners (8 listeners)
├── Models/                 # Eloquent models (28 models)
├── Providers/              # AppServiceProvider, EventServiceProvider
└── Services/               # Business logic layer (5 services)
```

### Event Flow

```
User Action
    └─► Controller
            └─► Service (business logic)
                    └─► Event fired
                            ├─► Listener 1 (e.g. GenerateLoanInstallmentSchedule)
                            ├─► Listener 2 (e.g. SendLoanApprovalNotification)
                            └─► Listener 3 (e.g. LogAuditTrail)
                                        └─► Job dispatched (SendEmailJob / SendSmsJob)
```

---

## API Reference

Base URL: `/api`
Authentication: Bearer token (Laravel Sanctum)

### Auth

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/login` | Login, returns token |
| POST | `/api/logout` | Revoke token |
| GET | `/api/me` | Current user info |

### Customers

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/customers` | List customers |
| POST | `/api/customers` | Register customer |
| GET | `/api/customers/{id}` | Get customer |
| PUT | `/api/customers/{id}` | Update customer |
| DELETE | `/api/customers/{id}` | Delete customer |
| POST | `/api/customers/{id}/approve` | Approve customer |
| POST | `/api/customers/{id}/reject` | Reject customer |

### Bank Accounts

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/bank-accounts` | List accounts |
| POST | `/api/bank-accounts` | Open account |
| GET | `/api/bank-accounts/{id}` | Get account |
| GET | `/api/bank-accounts/search/{accountNumber}` | Search by account number |

### Transactions

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/transactions` | List transactions |
| POST | `/api/transactions` | Create transaction |
| GET | `/api/transactions/{id}` | Get transaction |

### Loans

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/loans` | List loans |
| POST | `/api/loans` | Disburse loan |
| GET | `/api/loans/{id}` | Get loan |
| GET | `/api/loans/{id}/schedule` | Installment schedule |
| POST | `/api/loans/{id}/repayment` | Record repayment |

### FD Accounts

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/fd-accounts` | List FDs |
| POST | `/api/fd-accounts` | Open FD |
| GET | `/api/fd-accounts/{id}` | Get FD |

---

## Scheduled Tasks

Managed via `routes/console.php`:

| Command | Schedule | Description |
|---|---|---|
| `app:process-fd-maturity` | Daily 00:00 | Checks and processes matured FD accounts |
| `app:process-loan-od-interest` | Daily 00:01 | Posts overdraft interest to OD accounts |
| `queue:work --stop-when-empty` | Every minute | Drains the job queue |

Start the scheduler in production:

```bash
* * * * * cd /path/to/laravel-cooperative-bank && php artisan schedule:run >> /dev/null 2>&1
```

---

## Testing

The project uses **PHPUnit 12** with **SQLite in-memory** for fast, isolated tests. 109 feature tests cover authentication, role-based access, CRUD operations, business workflows, and input validation.

```bash
# Run all tests
php artisan test

# Run with coverage (requires Xdebug or PCOV)
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run a specific test file
php artisan test --filter=CustomerApprovalTest

# Run tests inside Docker
docker compose exec app php artisan test
```

### Test Structure

```
tests/Feature/
├── Auth/
│   └── LoginTest.php                  # Login, logout, redirects, validation
├── RoleAccess/
│   └── RoleAccessTest.php             # All 5 roles × all route groups
├── SuperAdmin/
│   ├── BranchTest.php                 # Branch CRUD
│   ├── UserManagementTest.php         # User CRUD, password handling
│   ├── LoanTypeTest.php              # Loan type CRUD
│   ├── FdSetupTest.php               # FD setup CRUD
│   ├── AccountTypeTest.php           # Account type CRUD
│   └── CompanySetupTest.php          # Company config
├── Manager/
│   ├── DashboardTest.php             # Dashboard, branch scoping
│   ├── CustomerApprovalTest.php      # Approve/reject, role enforcement
│   └── CustomerWorkflowTest.php      # End-to-end approval workflow
├── Clerk/
│   ├── CustomerRegistrationTest.php  # Registration, validation, scoping
│   └── LoanApplicationTest.php       # Submit, validate, create
├── Cashier/
│   ├── DashboardTest.php             # Dashboard, form access
│   └── TransactionTest.php           # Deposit, withdraw, validation
└── Accountant/
    └── DashboardTest.php             # Dashboard, role enforcement
```

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on how to contribute.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
