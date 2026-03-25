<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\{Customer, Loan, BankAccount, FdAccount, LoanApplication};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $stats = [
            'pending_customers'   => Customer::where('branch_id', $branchId)->where('approval_status', 'pending')->count(),
            'approved_customers'  => Customer::where('branch_id', $branchId)->where('approval_status', 'approved')->count(),
            'active_loans'        => Loan::where('branch_id', $branchId)->where('status', 'active')->count(),
            'pending_loans'       => LoanApplication::where('branch_id', $branchId)->where('approval_status', 'pending')->count(),
            'total_accounts'      => BankAccount::where('branch_id', $branchId)->where('is_active', true)->count(),
            'total_fd'            => FdAccount::where('branch_id', $branchId)->where('is_withdrawn', false)->count(),
        ];
        return view('manager.dashboard', compact('stats'));
    }
}
