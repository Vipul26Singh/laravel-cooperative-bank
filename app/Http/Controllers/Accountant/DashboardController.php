<?php
namespace App\Http\Controllers\Accountant;
use App\Http\Controllers\Controller;
use App\Models\{BankAccountTransaction, Loan, FdAccount};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;

        $stats = [
            'total_deposits'       => BankAccountTransaction::where('branch_id', $branchId)
                ->where('transaction_type', 'Deposit')
                ->sum('amount'),
            'total_withdrawals'    => BankAccountTransaction::where('branch_id', $branchId)
                ->where('transaction_type', 'Withdraw')
                ->sum('amount'),
            'outstanding_loans'    => Loan::where('branch_id', $branchId)
                ->where('status', 'active')
                ->sum('outstanding_balance'),
            'active_loans_count'   => Loan::where('branch_id', $branchId)
                ->where('status', 'active')
                ->count(),
            'fd_total'             => FdAccount::where('branch_id', $branchId)
                ->where('is_withdrawn', false)
                ->sum('principal_amount'),
            'fd_count'             => FdAccount::where('branch_id', $branchId)
                ->where('is_withdrawn', false)
                ->count(),
        ];

        return view('accountant.dashboard', compact('stats'));
    }
}
