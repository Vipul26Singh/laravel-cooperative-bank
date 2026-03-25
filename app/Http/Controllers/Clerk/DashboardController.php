<?php
namespace App\Http\Controllers\Clerk;
use App\Http\Controllers\Controller;
use App\Models\{Customer, LoanApplication, BankAccount};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId   = Auth::id();
        $branchId = Auth::user()->branch_id;

        $stats = [
            'my_pending_customers'   => Customer::where('created_by', $userId)
                ->where('approval_status', 'pending')
                ->count(),
            'my_total_customers'     => Customer::where('created_by', $userId)->count(),
            'my_loan_applications'   => LoanApplication::where('created_by', $userId)->count(),
            'my_pending_loans'       => LoanApplication::where('created_by', $userId)
                ->where('approval_status', 'pending')
                ->count(),
            'total_accounts_opened'  => BankAccount::where('created_by', $userId)->count(),
        ];

        return view('clerk.dashboard', compact('stats'));
    }
}
