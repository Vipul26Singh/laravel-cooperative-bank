<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\{Customer, Loan, User, Branch, BankAccount};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::where('approval_status', 'approved')->count(),
            'pending_customers' => Customer::where('approval_status', 'pending')->count(),
            'total_loans' => Loan::where('status', 'active')->count(),
            'total_branches' => Branch::where('is_active', true)->count(),
            'total_employees' => User::where('is_active', true)->count(),
            'total_accounts' => BankAccount::where('is_active', true)->count(),
        ];
        return view('superadmin.dashboard', compact('stats'));
    }
}
