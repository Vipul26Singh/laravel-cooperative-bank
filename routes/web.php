<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\{DashboardController as SuperAdminDashboard, BranchController, UserController, LoanTypeController, FdSetupController, AccountTypeController, CompanySetupController};
use App\Http\Controllers\Manager\{DashboardController as ManagerDashboard, CustomerController as ManagerCustomerController, BankAccountController, FdAccountController, LoanController, LoanApplicationController as ManagerLoanAppController};
use App\Http\Controllers\Clerk\{DashboardController as ClerkDashboard, CustomerController as ClerkCustomerController, LoanApplicationController as ClerkLoanAppController};
use App\Http\Controllers\Cashier\{DashboardController as CashierDashboard, BankTransactionController, LoanRepaymentController};
use App\Http\Controllers\Accountant\{DashboardController as AccountantDashboard, ReportController};

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
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

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
