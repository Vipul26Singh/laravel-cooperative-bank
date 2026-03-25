<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanApplicationController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $query = LoanApplication::where('branch_id', $branchId)
            ->with(['customer', 'loanType']);

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        } else {
            $query->whereIn('approval_status', ['pending', 'approved']);
        }

        $applications = $query->latest()->paginate(20);
        return view('manager.loan-applications.index', compact('applications'));
    }

    public function show(LoanApplication $loanApplication)
    {
        $loanApplication->load(['customer', 'loanType', 'guarantor1', 'guarantor2']);
        return view('manager.loan-applications.show', compact('loanApplication'));
    }

    public function approve(Request $request, LoanApplication $loanApplication)
    {
        $request->validate([
            'approved_amount' => 'nullable|numeric|min:0',
            'remarks'         => 'nullable|string|max:500',
        ]);

        $this->loanService->approveApplication($loanApplication, [
            'approved_amount' => $request->approved_amount ?? $loanApplication->applied_amount,
            'approved_by'     => Auth::id(),
            'remark'          => $request->remarks,
        ]);

        return redirect()->route('manager.loan-applications.show', $loanApplication->id)
            ->with('success', 'Loan application approved successfully.');
    }

    public function reject(Request $request, LoanApplication $loanApplication)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $this->loanService->rejectApplication($loanApplication, [
            'approved_by' => Auth::id(),
            'remark'      => $request->rejection_reason ?? '',
        ]);

        return redirect()->route('manager.loan-applications.show', $loanApplication->id)
            ->with('success', 'Loan application rejected.');
    }
}
