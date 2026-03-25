<?php
namespace App\Http\Controllers\Cashier;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRepaymentRequest;
use App\Models\{Loan, LoanTransaction};
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanRepaymentController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $today    = now()->toDateString();

        $collections = LoanTransaction::where('branch_id', $branchId)
            ->whereDate('payment_date', $today)
            ->with(['loan', 'loan.customer'])
            ->latest('payment_date')
            ->paginate(20);

        return view('cashier.loan-repayments.index', compact('collections'));
    }

    public function create(Request $request)
    {
        $loan = null;

        if ($request->filled('loan_number')) {
            $loan = Loan::where('loan_number', $request->loan_number)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('status', 'active')
                ->with(['customer', 'loanType'])
                ->first();
        }

        return view('cashier.loan-repayments.create', compact('loan'));
    }

    public function store(StoreLoanRepaymentRequest $request)
    {
        $data = array_merge($request->validated(), [
            'branch_id'  => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        $transaction = $this->loanService->recordRepayment($data);

        return redirect()->route('cashier.loan-repayments.show', $transaction->id)
            ->with('success', 'Loan repayment recorded successfully.');
    }

    public function show(LoanTransaction $loanTransaction)
    {
        $loanTransaction->load(['loan', 'loan.customer', 'loan.loanType']);
        return view('cashier.loan-repayments.show', compact('loanTransaction'));
    }
}
