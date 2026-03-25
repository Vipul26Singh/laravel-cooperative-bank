<?php
namespace App\Http\Controllers\Accountant;
use App\Http\Controllers\Controller;
use App\Models\{Loan, BankAccountTransaction, LoanTransaction, Customer};
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function loanOutstanding(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $loans = Loan::where('branch_id', $branchId)
            ->where('status', 'active')
            ->with(['customer', 'loanType'])
            ->get();
        return view('accountant.reports.loan-outstanding', compact('loans'));
    }

    public function transactionStatement(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $request->validate(['from_date' => 'required|date', 'to_date' => 'required|date|after_or_equal:from_date', 'account_number' => 'nullable']);
        $transactions = BankAccountTransaction::where('branch_id', $branchId)
            ->when($request->account_number, fn($q) => $q->where('account_number', $request->account_number))
            ->whereBetween('transaction_date', [$request->from_date, $request->to_date . ' 23:59:59'])
            ->with('customer')
            ->orderBy('transaction_date')
            ->get();
        return view('accountant.reports.transaction-statement', compact('transactions', 'request'));
    }

    public function loanDemand(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $loans = Loan::where('branch_id', $branchId)
            ->where('status', 'active')
            ->with(['customer', 'loanType', 'transactions' => fn($q) => $q->latest()->limit(1)])
            ->get();
        return view('accountant.reports.loan-demand', compact('loans'));
    }
}
