<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Customer, Loan, BankAccount, FdAccount, LoanApplication, BankAccountTransaction};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $branchId = $request->user()->branch_id;
        $roleName = $request->user()->role?->name;

        $query = fn($model) => $model::when(
            $branchId && $roleName !== 'SuperAdmin',
            fn($q) => $q->where('branch_id', $branchId)
        );

        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        $stats = [
            'customers' => [
                'total'   => $query(Customer::class)->where('approval_status', 'approved')->count(),
                'pending' => $query(Customer::class)->where('approval_status', 'pending')->count(),
            ],
            'loans' => [
                'active'   => $query(Loan::class)->where('status', 'active')->count(),
                'pending'  => $query(LoanApplication::class)->where('approval_status', 'pending')->count(),
                'total_outstanding' => $query(Loan::class)->where('status', 'active')->sum('outstanding_balance'),
            ],
            'accounts' => [
                'total'  => $query(BankAccount::class)->where('is_active', true)->count(),
                'total_balance' => $query(BankAccount::class)->where('is_active', true)->sum('balance'),
            ],
            'fd_accounts' => [
                'total'        => $query(FdAccount::class)->where('is_withdrawn', false)->count(),
                'matured'      => $query(FdAccount::class)->where('is_matured', true)->where('is_withdrawn', false)->count(),
                'total_amount' => $query(FdAccount::class)->where('is_withdrawn', false)->sum('principal_amount'),
            ],
            'today_transactions' => [
                'count'        => $query(BankAccountTransaction::class)->whereBetween('transaction_date', [$todayStart, $todayEnd])->count(),
                'total_deposit' => $query(BankAccountTransaction::class)->where('transaction_type', 'Deposit')->whereBetween('transaction_date', [$todayStart, $todayEnd])->sum('amount'),
                'total_withdraw' => $query(BankAccountTransaction::class)->where('transaction_type', 'Withdraw')->whereBetween('transaction_date', [$todayStart, $todayEnd])->sum('amount'),
            ],
        ];

        return response()->json($stats);
    }
}
