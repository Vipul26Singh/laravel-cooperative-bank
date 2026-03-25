<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CustomerApiController,
    BankAccountApiController,
    LoanApiController,
    FdAccountApiController,
    TransactionApiController,
    DashboardApiController,
};

/*
|--------------------------------------------------------------------------
| API Routes — Cooperative Bank ERP
|--------------------------------------------------------------------------
| All routes are prefixed with /api (configured in bootstrap/app.php)
| Authentication: Laravel Sanctum token-based
*/

// Public: Auth
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard stats
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);

    // Customers
    Route::apiResource('customers', CustomerApiController::class);
    Route::post('/customers/{customer}/approve', [CustomerApiController::class, 'approve']);
    Route::post('/customers/{customer}/reject', [CustomerApiController::class, 'reject']);

    // Bank Accounts
    Route::apiResource('bank-accounts', BankAccountApiController::class);
    Route::get('/bank-accounts/search/{accountNumber}', [BankAccountApiController::class, 'findByNumber']);

    // Transactions
    Route::apiResource('transactions', TransactionApiController::class)->only(['index', 'store', 'show']);

    // FD Accounts
    Route::apiResource('fd-accounts', FdAccountApiController::class);

    // Loans
    Route::apiResource('loans', LoanApiController::class);
    Route::get('/loans/{loan}/schedule', [LoanApiController::class, 'installmentSchedule']);
    Route::post('/loans/{loan}/repayment', [LoanApiController::class, 'recordRepayment']);
});
