<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Models\{Loan, LoanApplication, LoanType};
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $query = Loan::where('branch_id', $branchId)->with(['customer', 'loanType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(20);
        return view('manager.loans.index', compact('loans'));
    }

    public function create()
    {
        $branchId         = Auth::user()->branch_id;
        $loanApplications = LoanApplication::where('branch_id', $branchId)
            ->where('approval_status', 'approved')
            ->whereDoesntHave('loan')
            ->with('customer')
            ->get();
        $loanTypes = LoanType::where('is_active', true)->get();
        return view('manager.loans.create', compact('loanApplications', 'loanTypes'));
    }

    public function store(StoreLoanRequest $request)
    {
        $data = array_merge($request->validated(), [
            'branch_id'  => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        $loan = $this->loanService->disburseLoan($data);

        return redirect()->route('manager.loans.show', $loan->id)
            ->with('success', 'Loan disbursed successfully.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['customer', 'loanType', 'transactions', 'installments']);
        return view('manager.loans.show', compact('loan'));
    }

    public function installmentSchedule(Loan $loan)
    {
        $schedule = $loan->installments()->orderBy('due_date')->get();

        if (request()->wantsJson()) {
            return response()->json($schedule);
        }

        return view('manager.loans.installment-schedule', compact('loan', 'schedule'));
    }
}
