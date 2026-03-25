<?php
namespace App\Http\Controllers\Clerk;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Models\{LoanApplication, Customer, LoanType};
use App\Services\LoanService;
use Illuminate\Support\Facades\Auth;

class LoanApplicationController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index()
    {
        $applications = LoanApplication::where('created_by', Auth::id())
            ->with(['customer', 'loanType'])
            ->latest()
            ->paginate(20);

        return view('clerk.loan-applications.index', compact('applications'));
    }

    public function create()
    {
        $branchId  = Auth::user()->branch_id;
        $customers = Customer::where('branch_id', $branchId)
            ->where('approval_status', 'approved')
            ->get();
        $loanTypes = LoanType::where('is_active', true)->get();

        return view('clerk.loan-applications.create', compact('customers', 'loanTypes'));
    }

    public function store(StoreLoanApplicationRequest $request)
    {
        $data = array_merge($request->validated(), [
            'branch_id'  => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        $application = $this->loanService->submitApplication($data);

        return redirect()->route('clerk.loan-applications.show', $application->id)
            ->with('success', 'Loan application submitted successfully.');
    }

    public function show(LoanApplication $loanApplication)
    {
        $loanApplication->load(['customer', 'loanType', 'guarantor1', 'guarantor2']);
        return view('clerk.loan-applications.show', compact('loanApplication'));
    }
}
