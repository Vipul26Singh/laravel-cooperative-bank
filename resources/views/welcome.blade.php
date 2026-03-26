<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CoopBank ERP — Cooperative Bank Management System</title>
    <meta name="description" content="Open-source core banking solution for cooperative banks, credit societies, and microfinance institutions. Manage customers, loans, deposits, FDs, and more.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1a73e8; --dark: #0d1b2a; --accent: #00b894; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; color: #333; }
        .hero { background: linear-gradient(135deg, var(--dark) 0%, #1b2838 50%, #243447 100%); color: #fff; padding: 100px 0 80px; position: relative; overflow: hidden; }
        .hero::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 80px; background: #fff; clip-path: polygon(0 100%, 100% 100%, 100% 0); }
        .hero h1 { font-size: 3rem; font-weight: 700; line-height: 1.2; }
        .hero .lead { font-size: 1.25rem; opacity: 0.85; max-width: 600px; }
        .btn-hero { padding: 14px 36px; font-size: 1.1rem; border-radius: 8px; font-weight: 600; }
        .btn-primary-custom { background: var(--primary); border: none; color: #fff; }
        .btn-primary-custom:hover { background: #1557b0; color: #fff; }
        .btn-outline-light:hover { background: rgba(255,255,255,0.15); }
        .feature-card { border: none; border-radius: 16px; padding: 32px 24px; height: 100%; transition: transform 0.2s, box-shadow 0.2s; background: #fff; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
        .feature-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 16px; }
        .section-title { font-size: 2rem; font-weight: 700; margin-bottom: 12px; }
        .section-subtitle { color: #666; font-size: 1.1rem; max-width: 600px; margin: 0 auto 48px; }
        .stats-section { background: var(--dark); color: #fff; padding: 60px 0; }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: var(--accent); }
        .screenshot-section { background: #f8f9fa; padding: 80px 0; }
        .screenshot-img { border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); max-width: 100%; }
        .cta-section { background: linear-gradient(135deg, var(--primary), #0056b3); color: #fff; padding: 80px 0; }
        .tech-badge { display: inline-block; background: #f0f0f0; padding: 6px 16px; border-radius: 20px; margin: 4px; font-size: 0.85rem; font-weight: 500; color: #444; }
        .footer { background: var(--dark); color: rgba(255,255,255,0.6); padding: 40px 0; }
        .footer a { color: rgba(255,255,255,0.8); text-decoration: none; }
        .footer a:hover { color: #fff; }
        .navbar-landing { background: transparent; transition: background 0.3s; }
        .navbar-landing.scrolled { background: var(--dark); box-shadow: 0 2px 20px rgba(0,0,0,0.2); }
        @media (max-width: 768px) { .hero h1 { font-size: 2rem; } .hero { padding: 80px 0 60px; } }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-landing" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="#"><i class="fas fa-university me-2"></i>CoopBank ERP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#screenshots">Screenshots</a></li>
                <li class="nav-item"><a class="nav-link" href="#roles">Roles</a></li>
                <li class="nav-item"><a class="nav-link" href="#tech">Tech Stack</a></li>
            </ul>
            <a href="/login" class="btn btn-outline-light btn-sm px-4">Staff Login</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero text-center">
    <div class="container">
        <span class="badge bg-success bg-opacity-25 text-light mb-3 px-3 py-2" style="font-size:0.85rem;"><i class="fas fa-check-circle me-1"></i> Open Source &middot; Self-Hosted &middot; Free Forever</span>
        <h1>Modern Banking Software<br>for Cooperative Banks</h1>
        <p class="lead mx-auto mt-3">Complete core banking solution — customer onboarding, loans, fixed deposits, transactions, and financial reporting. Deploy in one command.</p>
        <div class="mt-4">
            <a href="/login" class="btn btn-hero btn-primary-custom me-2"><i class="fas fa-sign-in-alt me-2"></i>Live Demo</a>
            <a href="https://github.com/vipul26singh/laravel-cooperative-bank" class="btn btn-hero btn-outline-light"><i class="fab fa-github me-2"></i>View on GitHub</a>
        </div>
        <p class="mt-3 small opacity-50">Demo: admin@coopbank.com / Admin@123</p>
    </div>
</section>

<!-- Stats -->
<section class="stats-section">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3"><div class="stat-number">5</div><div class="opacity-75">User Roles</div></div>
            <div class="col-6 col-md-3"><div class="stat-number">184+</div><div class="opacity-75">Automated Tests</div></div>
            <div class="col-6 col-md-3"><div class="stat-number">50+</div><div class="opacity-75">Screens & Pages</div></div>
            <div class="col-6 col-md-3"><div class="stat-number">1</div><div class="opacity-75">Command to Deploy</div></div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="py-5" style="padding:80px 0;">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Everything a Cooperative Bank Needs</h2>
            <p class="section-subtitle">From customer walk-in to loan closure — manage the complete banking lifecycle.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card shadow-sm">
                    <div class="feature-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-users"></i></div>
                    <h5 class="fw-bold">Customer Management</h5>
                    <p class="text-muted mb-0">Registration, KYC verification (PAN + Aadhaar), multi-level approval workflow. Bulk approve pending customers in one click.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card shadow-sm">
                    <div class="feature-icon bg-success bg-opacity-10 text-success"><i class="fas fa-hand-holding-usd"></i></div>
                    <h5 class="fw-bold">Loan Management</h5>
                    <p class="text-muted mb-0">Application to closure — approval, disbursement, EMI schedule generation, repayment tracking. Daily, weekly, and monthly frequencies.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card shadow-sm">
                    <div class="feature-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-piggy-bank"></i></div>
                    <h5 class="fw-bold">Fixed Deposits</h5>
                    <p class="text-muted mb-0">Scheme-based FD creation with auto-maturity processing, interest calculation, and senior citizen special rates.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card shadow-sm">
                    <div class="feature-icon bg-info bg-opacity-10 text-info"><i class="fas fa-exchange-alt"></i></div>
                    <h5 class="fw-bold">Transactions</h5>
                    <p class="text-muted mb-0">Cash and cheque deposits/withdrawals with real-time balance updates, insufficient-balance protection, and printable receipts.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card shadow-sm">
                    <div class="feature-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-chart-bar"></i></div>
                    <h5 class="fw-bold">Reports & CSV Export</h5>
                    <p class="text-muted mb-0">Loan outstanding, transaction statements, demand collection sheets. Download as CSV or print directly from the browser.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card shadow-sm">
                    <div class="feature-icon bg-dark bg-opacity-10"><i class="fas fa-clock"></i></div>
                    <h5 class="fw-bold">Task Scheduler & Queue Monitor</h5>
                    <p class="text-muted mb-0">Visual dashboard for all scheduled tasks — enable, disable, edit timing, run manually. Monitor pending and failed background jobs in real-time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Screenshots -->
<section id="screenshots" class="screenshot-section">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">See It in Action</h2>
            <p class="section-subtitle">Real screenshots auto-generated by our browser test suite — what you see is what you get.</p>
        </div>
        <div id="screenshotCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="text-center"><img src="/docs/screenshots/superadmin-dashboard.png" class="screenshot-img" alt="SuperAdmin Dashboard" style="max-height:500px;"><p class="mt-3 fw-bold">SuperAdmin Dashboard</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/customer-registration.png" class="screenshot-img" alt="Customer Registration" style="max-height:500px;"><p class="mt-3 fw-bold">Customer Registration (Clerk)</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/transaction-form.png" class="screenshot-img" alt="Transaction Form" style="max-height:500px;"><p class="mt-3 fw-bold">Bank Transaction (Cashier)</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/manager-dashboard.png" class="screenshot-img" alt="Manager Dashboard" style="max-height:500px;"><p class="mt-3 fw-bold">Manager Dashboard</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/loan-outstanding-report.png" class="screenshot-img" alt="Reports" style="max-height:500px;"><p class="mt-3 fw-bold">Loan Outstanding Report (Accountant)</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/branch-details.png" class="screenshot-img" alt="Branch Details" style="max-height:500px;"><p class="mt-3 fw-bold">Branch Details (SuperAdmin)</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/task-scheduler.png" class="screenshot-img" alt="Task Scheduler" style="max-height:500px;"><p class="mt-3 fw-bold">Task Scheduler (SuperAdmin)</p></div>
                </div>
                <div class="carousel-item">
                    <div class="text-center"><img src="/docs/screenshots/queue-monitor.png" class="screenshot-img" alt="Queue Monitor" style="max-height:500px;"><p class="mt-3 fw-bold">Queue Monitor (SuperAdmin)</p></div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#screenshotCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#screenshotCarousel" data-bs-slide="next"><span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span></button>
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="0" class="active bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="1" class="bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="2" class="bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="3" class="bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="4" class="bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="5" class="bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="6" class="bg-dark"></button>
                <button type="button" data-bs-target="#screenshotCarousel" data-bs-slide-to="7" class="bg-dark"></button>
            </div>
        </div>
    </div>
</section>

<!-- Roles -->
<section id="roles" class="py-5" style="padding:80px 0;">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">5 Dedicated Roles</h2>
            <p class="section-subtitle">Each role sees only what they need — scoped dashboards, menus, and data access.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-lg">
                <div class="text-center p-4"><div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="fas fa-crown text-primary fa-lg"></i></div><h6 class="fw-bold">SuperAdmin</h6><p class="text-muted small">Branches, users, loan types, FD schemes, account types, company setup</p></div>
            </div>
            <div class="col-md-4 col-lg">
                <div class="text-center p-4"><div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="fas fa-user-tie text-success fa-lg"></i></div><h6 class="fw-bold">Manager</h6><p class="text-muted small">Approve customers & loans, open accounts & FDs, disburse loans, bulk operations</p></div>
            </div>
            <div class="col-md-4 col-lg">
                <div class="text-center p-4"><div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="fas fa-user-edit text-info fa-lg"></i></div><h6 class="fw-bold">Clerk</h6><p class="text-muted small">Register customers, submit loan applications, front-office data entry</p></div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="text-center p-4"><div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="fas fa-cash-register text-warning fa-lg"></i></div><h6 class="fw-bold">Cashier</h6><p class="text-muted small">Process deposits, withdrawals, and loan repayments at the counter</p></div>
            </div>
            <div class="col-md-6 col-lg">
                <div class="text-center p-4"><div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="fas fa-calculator text-danger fa-lg"></i></div><h6 class="fw-bold">Accountant</h6><p class="text-muted small">Loan outstanding, transaction statements, demand collection — all exportable</p></div>
            </div>
        </div>
    </div>
</section>

<!-- Tech Stack -->
<section id="tech" style="padding:60px 0; background:#f8f9fa;">
    <div class="container text-center">
        <h2 class="section-title">Built With</h2>
        <p class="section-subtitle">Modern, battle-tested stack. No vendor lock-in.</p>
        <div>
            <span class="tech-badge"><i class="fab fa-laravel text-danger me-1"></i> Laravel 13</span>
            <span class="tech-badge"><i class="fab fa-php text-primary me-1"></i> PHP 8.4</span>
            <span class="tech-badge"><i class="fas fa-database text-success me-1"></i> SQLite / MySQL</span>
            <span class="tech-badge"><i class="fab fa-bootstrap me-1" style="color:#7952b3;"></i> AdminLTE 3</span>
            <span class="tech-badge"><i class="fab fa-docker text-info me-1"></i> Docker</span>
            <span class="tech-badge"><i class="fas fa-vial text-warning me-1"></i> 184+ Tests</span>
            <span class="tech-badge"><i class="fas fa-lock text-dark me-1"></i> Sanctum API</span>
            <span class="tech-badge"><i class="fab fa-js text-warning me-1"></i> Vite 8</span>
            <span class="tech-badge"><i class="fas fa-search text-info me-1"></i> Select2</span>
        </div>
    </div>
</section>

<!-- Quick Start -->
<section style="padding:80px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="section-title">Up and Running in 60 Seconds</h2>
                <p class="text-muted">One command. No PHP, Node, or Composer needed on your machine. Just Docker.</p>
                <ul class="text-muted">
                    <li>Auto-creates database and runs migrations</li>
                    <li>Seeds default admin user and roles</li>
                    <li>Builds frontend assets inside the container</li>
                    <li>Starts web server, queue worker, and scheduler</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="bg-dark text-white p-4 rounded-3" style="font-family:monospace;">
                    <div class="text-success mb-2"># Clone and start</div>
                    <div class="mb-1"><span class="text-info">$</span> git clone https://github.com/vipul26singh/laravel-cooperative-bank.git</div>
                    <div class="mb-1"><span class="text-info">$</span> cd laravel-cooperative-bank</div>
                    <div class="mb-3"><span class="text-info">$</span> docker compose up -d</div>
                    <div class="text-success mb-2"># Open in browser</div>
                    <div><span class="text-info">$</span> open http://localhost:8000</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section text-center">
    <div class="container">
        <h2 class="fw-bold mb-3" style="font-size:2.2rem;">Ready to Modernize Your Bank?</h2>
        <p class="lead opacity-75 mb-4" style="max-width:600px;margin:0 auto;">Free, open-source, and built for Indian cooperative banks. Deploy today, customize tomorrow.</p>
        <a href="/login" class="btn btn-hero btn-light text-primary me-2"><i class="fas fa-sign-in-alt me-2"></i>Try the Demo</a>
        <a href="https://github.com/vipul26singh/laravel-cooperative-bank" class="btn btn-hero btn-outline-light"><i class="fab fa-github me-2"></i>Star on GitHub</a>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h6 class="text-white fw-bold"><i class="fas fa-university me-2"></i>CoopBank ERP</h6>
                <p class="small">Open-source core banking solution for cooperative banks, credit societies, and microfinance institutions.</p>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="text-white fw-bold">Product</h6>
                <ul class="list-unstyled small">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#screenshots">Screenshots</a></li>
                    <li><a href="#roles">User Roles</a></li>
                    <li><a href="/login">Staff Login</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="text-white fw-bold">Resources</h6>
                <ul class="list-unstyled small">
                    <li><a href="https://github.com/vipul26singh/laravel-cooperative-bank">GitHub Repository</a></li>
                    <li><a href="https://github.com/vipul26singh/laravel-cooperative-bank/blob/main/CONTRIBUTING.md">Contributing Guide</a></li>
                    <li><a href="https://github.com/vipul26singh/laravel-cooperative-bank/blob/main/USER_MANUAL.md">User Manual</a></li>
                    <li><a href="https://github.com/vipul26singh/laravel-cooperative-bank/blob/main/CUSTOMER_PORTAL_ARCHITECTURE.md">Portal Architecture</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="text-white fw-bold">Quick Start</h6>
                <code class="small text-info">docker compose up -d</code>
                <p class="small mt-2">Demo credentials:<br>admin@coopbank.com / Admin@123</p>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.1);">
        <div class="text-center small">
            <p class="mb-0">&copy; {{ date('Y') }} CoopBank ERP. Open-sourced under the <a href="https://opensource.org/licenses/MIT">MIT License</a>.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('scroll', function() {
    document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 50);
});
</script>
</body>
</html>
