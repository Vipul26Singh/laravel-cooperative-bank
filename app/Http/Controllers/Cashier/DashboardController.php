<?php
namespace App\Http\Controllers\Cashier;
use App\Http\Controllers\Controller;
use App\Models\{BankAccountTransaction, LoanTransaction};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $today    = now()->toDateString();

        $stats = [
            'transactions_today_count' => BankAccountTransaction::where('branch_id', $branchId)
                ->whereDate('transaction_date', $today)
                ->count(),
            'transactions_today_sum'   => BankAccountTransaction::where('branch_id', $branchId)
                ->whereDate('transaction_date', $today)
                ->sum('amount'),
            'deposits_today'           => BankAccountTransaction::where('branch_id', $branchId)
                ->where('transaction_type', 'Deposit')
                ->whereDate('transaction_date', $today)
                ->sum('amount'),
            'withdrawals_today'        => BankAccountTransaction::where('branch_id', $branchId)
                ->where('transaction_type', 'Withdraw')
                ->whereDate('transaction_date', $today)
                ->sum('amount'),
            'loans_collected_today'    => LoanTransaction::where('branch_id', $branchId)
                ->whereDate('payment_date', $today)
                ->sum('amount_paid'),
        ];

        return view('cashier.dashboard', compact('stats'));
    }
}
