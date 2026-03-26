<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, ProfileController};
use App\Http\Controllers\SuperAdmin\{DashboardController as SuperAdminDashboard, BranchController, UserController, LoanTypeController, FdSetupController, AccountTypeController, CompanySetupController};
use App\Http\Controllers\Manager\{DashboardController as ManagerDashboard, CustomerController as ManagerCustomerController, BankAccountController, FdAccountController, LoanController, LoanApplicationController as ManagerLoanAppController};
use App\Http\Controllers\Clerk\{DashboardController as ClerkDashboard, CustomerController as ClerkCustomerController, LoanApplicationController as ClerkLoanAppController};
use App\Http\Controllers\Cashier\{DashboardController as CashierDashboard, BankTransactionController, LoanRepaymentController};
use App\Http\Controllers\Accountant\{DashboardController as AccountantDashboard, ReportController};

// Installation Wizard
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [\App\Http\Controllers\InstallController::class, 'welcome'])->name('welcome');
    Route::get('/requirements', [\App\Http\Controllers\InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database', [\App\Http\Controllers\InstallController::class, 'database'])->name('database');
    Route::post('/database', [\App\Http\Controllers\InstallController::class, 'saveDatabase']);
    Route::get('/admin', [\App\Http\Controllers\InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [\App\Http\Controllers\InstallController::class, 'saveAdmin']);
    Route::get('/finish', [\App\Http\Controllers\InstallController::class, 'finish'])->name('finish');
    Route::post('/run', [\App\Http\Controllers\InstallController::class, 'run'])->name('run');
    Route::get('/complete', [\App\Http\Controllers\InstallController::class, 'complete'])->name('complete');
});

// Home — redirect authenticated users to their dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(match (auth()->user()->role?->name) {
            'SuperAdmin' => 'superadmin.dashboard',
            'Manager'    => 'manager.dashboard',
            'Clerk'      => 'clerk.dashboard',
            'Cashier'    => 'cashier.dashboard',
            'Accountant' => 'accountant.dashboard',
            default      => 'login',
        });
    }
    return view('welcome');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Password Reset
    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
    Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));
        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent to your email.')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');
    Route::get('/reset-password/{token}', fn(string $token) => view('auth.reset-password', ['token' => $token, 'email' => request('email')]))->name('password.reset');
    Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
        $request->validate(['token' => 'required', 'email' => 'required|email', 'password' => 'required|min:8|confirmed']);
        $status = \Illuminate\Support\Facades\Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill(['password' => \Illuminate\Support\Facades\Hash::make($password)])->save();
        });
        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password has been reset. Please login.')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// SuperAdmin Routes
Route::middleware(['auth', 'role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('branches', BranchController::class);
    Route::resource('users', UserController::class);
    Route::resource('loan-types', LoanTypeController::class);
    Route::resource('fd-setups', FdSetupController::class);
    Route::resource('account-types', AccountTypeController::class);
    Route::get('/company-setup', [CompanySetupController::class, 'show'])->name('company-setup.show');
    Route::put('/company-setup', [CompanySetupController::class, 'update'])->name('company-setup.update');
});

// Manager Routes
Route::middleware(['auth', 'role:Manager,SuperAdmin'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboard::class, 'index'])->name('dashboard');
    Route::get('/customers', [ManagerCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [ManagerCustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{customer}/approve', [ManagerCustomerController::class, 'approve'])->name('customers.approve');
    Route::post('/customers/{customer}/reject', [ManagerCustomerController::class, 'reject'])->name('customers.reject');
    Route::post('/customers/bulk-approve', [ManagerCustomerController::class, 'bulkApprove'])->name('customers.bulk-approve');
    Route::resource('bank-accounts', BankAccountController::class)->only(['index','create','store','show']);
    Route::resource('fd-accounts', FdAccountController::class)->only(['index','create','store','show']);
    Route::resource('loans', LoanController::class)->only(['index','create','store','show']);
    Route::get('/loans/{loan}/schedule', [LoanController::class, 'installmentSchedule'])->name('loans.schedule');
    Route::get('/loan-applications', [ManagerLoanAppController::class, 'index'])->name('loan-applications.index');
    Route::get('/loan-applications/{loanApplication}', [ManagerLoanAppController::class, 'show'])->name('loan-applications.show');
    Route::post('/loan-applications/{loanApplication}/approve', [ManagerLoanAppController::class, 'approve'])->name('loan-applications.approve');
    Route::post('/loan-applications/{loanApplication}/reject', [ManagerLoanAppController::class, 'reject'])->name('loan-applications.reject');
});

// Clerk Routes
Route::middleware(['auth', 'role:Clerk,Manager,SuperAdmin'])->prefix('clerk')->name('clerk.')->group(function () {
    Route::get('/dashboard', [ClerkDashboard::class, 'index'])->name('dashboard');
    Route::resource('customers', ClerkCustomerController::class)->only(['index','create','store','show']);
    Route::resource('loan-applications', ClerkLoanAppController::class)->only(['index','create','store','show']);
});

// Cashier Routes
Route::middleware(['auth', 'role:Cashier,Manager,SuperAdmin'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', [CashierDashboard::class, 'index'])->name('dashboard');
    Route::resource('transactions', BankTransactionController::class)->only(['index','create','store','show']);
    Route::resource('loan-repayments', LoanRepaymentController::class)->only(['index','create','store','show']);
});

// Accountant Routes
Route::middleware(['auth', 'role:Accountant,Manager,SuperAdmin'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantDashboard::class, 'index'])->name('dashboard');
    Route::get('/reports/loan-outstanding', [ReportController::class, 'loanOutstanding'])->name('reports.loan-outstanding');
    Route::get('/reports/transaction-statement', [ReportController::class, 'transactionStatement'])->name('reports.transaction-statement');
    Route::get('/reports/loan-demand', [ReportController::class, 'loanDemand'])->name('reports.loan-demand');
});
