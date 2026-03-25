# Customer Portal — Architecture & Design

This document outlines the technical architecture for the customer-facing self-service portal and the payment gateway integration planned for Phase 2.

---

## Overview

The customer portal extends the existing cooperative banking ERP into a digital banking solution — allowing customers to view their accounts, track loans, download statements, and (in Phase 2) make payments online.

```
┌─────────────────────────────────────────────────────────────────┐
│                    Cooperative Bank Platform                     │
│                                                                 │
│  ┌──────────────────────┐    ┌────────────────────────────────┐ │
│  │    Admin Panel        │    │     Customer Portal            │ │
│  │    (Staff Only)       │    │     (Customer Self-Service)    │ │
│  │                       │    │                                │ │
│  │  /superadmin/*        │    │  /portal/dashboard             │ │
│  │  /manager/*           │    │  /portal/accounts              │ │
│  │  /clerk/*             │    │  /portal/loans                 │ │
│  │  /cashier/*           │    │  /portal/fd                    │ │
│  │  /accountant/*        │    │  /portal/statements            │ │
│  │                       │    │  /portal/payments (Phase 2)    │ │
│  └───────────┬───────────┘    └───────────────┬────────────────┘ │
│              │                                │                  │
│              └──────────┬─────────────────────┘                  │
│                         │                                        │
│              ┌──────────▼──────────┐                             │
│              │   Shared Backend    │                             │
│              │                     │                             │
│              │  Services Layer     │    ← Business logic         │
│              │  Eloquent Models    │    ← Data access            │
│              │  Events/Listeners   │    ← Notifications          │
│              │  REST API           │    ← Mobile / SPA clients   │
│              └──────────┬──────────┘                             │
│                         │                                        │
│              ┌──────────▼──────────┐                             │
│              │     Database        │                             │
│              │     (SQLite/MySQL)  │                             │
│              └─────────────────────┘                             │
└─────────────────────────────────────────────────────────────────┘
```

---

## Authentication Strategy

**Recommended: Separate auth guard for customers.**

Staff and customers are fundamentally different user types — mixing them in one table creates security risks and complicates middleware.

| Aspect | Staff (Admin Panel) | Customer (Portal) |
|---|---|---|
| Table | `users` | `customers` (add `password` column) |
| Auth guard | `web` (default) | `customer` (custom guard) |
| Login method | Email + password | Mobile + password / OTP |
| Session | `auth` middleware | `auth:customer` middleware |
| API tokens | Sanctum (`users`) | Sanctum (`customers`) |
| Registration | SuperAdmin creates | Self-register or auto on approval |

### Implementation

```php
// config/auth.php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'customer' => ['driver' => 'session', 'provider' => 'customers'],
],
'providers' => [
    'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
    'customers' => ['driver' => 'eloquent', 'model' => App\Models\Customer::class],
],
```

### Migration needed

```php
// Add to customers table
$table->string('password')->nullable(); // Set on approval or self-registration
$table->string('login_mobile', 20)->nullable()->unique(); // For OTP login
$table->rememberToken();
```

---

## Phase 1 — Read-Only Portal

### Routes

```
GET  /portal/login              → Customer login page
POST /portal/login              → Authenticate customer
POST /portal/logout             → Logout
GET  /portal/dashboard          → Account summary, active loans, upcoming EMIs
GET  /portal/accounts           → List bank accounts with balances
GET  /portal/accounts/{id}      → Transaction history with date filter
GET  /portal/loans              → Active loans with outstanding + next EMI date
GET  /portal/loans/{id}         → Full EMI schedule with paid/pending status
GET  /portal/fd                 → FD accounts with maturity dates
GET  /portal/statements         → Download PDF account statement (DomPDF)
GET  /portal/profile            → View/update contact info, nominee
```

### Controller Structure

```
app/Http/Controllers/Portal/
├── AuthController.php           # Login, logout, OTP verification
├── DashboardController.php      # Summary stats for the customer
├── AccountController.php        # Bank account list + transaction history
├── LoanController.php           # Loan details + EMI schedule
├── FdController.php             # FD account details
├── StatementController.php      # PDF generation and download
└── ProfileController.php        # Customer profile management
```

### Views

```
resources/views/portal/
├── layouts/
│   └── app.blade.php            # Clean customer-facing layout (not AdminLTE)
├── auth/
│   ├── login.blade.php
│   └── otp.blade.php
├── dashboard.blade.php
├── accounts/
│   ├── index.blade.php
│   └── show.blade.php
├── loans/
│   ├── index.blade.php
│   └── show.blade.php
├── fd/
│   └── index.blade.php
├── statements/
│   └── index.blade.php
└── profile.blade.php
```

### Key Design Decisions

1. **Separate layout** — Customer portal should NOT use AdminLTE. Use a clean, mobile-first layout (Bootstrap 5 or Tailwind). Bank customers access this on phones.

2. **Read-only first** — Phase 1 has zero write operations. Customers can only view data. This simplifies security review.

3. **Statement PDF** — Use DomPDF (`barryvdh/laravel-dompdf`) to generate bank-style account statements with header, logo, and transaction table.

4. **Data scoping** — Every query in Portal controllers MUST be scoped to `auth('customer')->id()`. Never expose other customers' data.

---

## Phase 2 — Payment Gateway Integration

### Recommended Gateway: Razorpay

Best fit for Indian cooperative banks — supports UPI, cards, net banking, auto-settlement, and has good Laravel packages.

### Database: `payments` table

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained();
    $table->string('payment_type', 30);        // emi, fd_opening, deposit
    $table->morphs('payable');                  // Polymorphic: Loan, FdAccount, BankAccount
    $table->decimal('amount', 15, 2);
    $table->string('gateway', 20);             // razorpay, paytm
    $table->string('gateway_order_id')->nullable();
    $table->string('gateway_payment_id')->nullable();
    $table->string('status', 20)->default('initiated'); // initiated, paid, failed, refunded
    $table->json('gateway_response')->nullable();
    $table->timestamps();
});
```

### Payment Flow

```
Customer clicks "Pay EMI"
    │
    ▼
Portal creates Payment record (status: initiated)
    │
    ▼
Razorpay checkout opens (client-side JS)
    │
    ▼
Customer pays via UPI / Card / Net Banking
    │
    ▼
Razorpay webhook hits /api/webhooks/razorpay
    │
    ▼
WebhookController verifies signature
    │
    ▼
Payment record updated (status: paid, gateway_payment_id set)
    │
    ▼
LoanService::recordRepayment() auto-called
    │
    ▼
Event fired → Notification sent to customer
```

### Payout Flow (FD Maturity / Loan Disbursement)

```
Scheduled job detects matured FD or approved loan
    │
    ▼
PayoutService creates Razorpay payout via API
    │
    ▼
Funds transferred to customer's linked bank account
    │
    ▼
Payout record updated → Notification sent
```

### Controller Structure

```
app/Http/Controllers/Portal/
├── PaymentController.php        # Initiate payment, verify, history
└── WebhookController.php        # Handle Razorpay/Paytm callbacks

app/Services/
├── PaymentService.php           # Create order, verify, record
└── PayoutService.php            # Initiate payouts via gateway API
```

### API Endpoints (for mobile/SPA)

```
POST /api/portal/payments/initiate    → Create payment order
POST /api/portal/payments/verify      → Verify after gateway callback
GET  /api/portal/payments             → Payment history
POST /api/webhooks/razorpay           → Gateway webhook (public, signature-verified)
```

---

## Security Considerations

| Risk | Mitigation |
|---|---|
| Customer sees another customer's data | All queries scoped to `auth('customer')->id()` |
| Payment tampering | Server-side amount verification, Razorpay signature check |
| Replay attacks on webhooks | Idempotency via `gateway_payment_id` uniqueness |
| Session hijacking | HTTPS-only, SameSite cookies, CSRF on all forms |
| OTP brute force | Rate limit OTP attempts (5/min), exponential backoff |
| Sensitive data exposure | Never return full account numbers in API — mask as `XXXX1234` |

---

## Tech Stack Additions

| Component | Technology | Purpose |
|---|---|---|
| PDF generation | `barryvdh/laravel-dompdf` | Account statements |
| Payment gateway | `razorpay/razorpay` | Online payments |
| OTP | Custom or `laravel-otp` | Mobile login |
| Frontend (Portal) | Bootstrap 5 or Tailwind | Clean mobile-first UI |
| Queue | Redis (upgrade from DB) | Handle webhooks reliably |

---

## Migration Plan

1. Add `password`, `login_mobile`, `remember_token` to `customers` table
2. Create `payments` table
3. Create `customer` auth guard
4. Build Portal controllers + views
5. Integrate Razorpay (test mode first)
6. Add webhook handler
7. End-to-end testing with Dusk
8. Deploy behind feature flag, gradual rollout
