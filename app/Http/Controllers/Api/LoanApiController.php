<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanApiController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(Request $request): JsonResponse
    {
        $loans = Loan::where('branch_id', $request->user()->branch_id)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->customer_id, fn($q, $c) => $q->where('customer_id', $c))
            ->with(['customer', 'loanType'])
            ->paginate(20);

        return response()->json($loans);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'loan_type_id'         => 'required|exists:loan_types,id',
            'loan_application_id'  => 'nullable|exists:loan_applications,id',
            'amount'               => 'required|numeric|min:100',
            'interest_rate'        => 'required|numeric|min:0|max:100',
            'duration_months'      => 'required|integer|min:1|max:360',
            'frequency'            => 'required|in:DAILY,WEEKLY,MONTHLY',
            'first_installment_date' => 'required|date',
            'guarantor1_id'        => 'nullable|exists:customers,id',
            'guarantor2_id'        => 'nullable|exists:customers,id',
            'remarks'              => 'nullable|string',
        ]);

        $data['branch_id']  = $request->user()->branch_id;
        $data['created_by'] = $request->user()->id;
        $data['loan_date']  = now()->toDateString();

        $loan = $this->loanService->disburseLoan($data);

        return response()->json($loan->load(['customer', 'loanType']), 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        return response()->json($loan->load(['customer', 'loanType', 'transactions', 'guarantor1', 'guarantor2']));
    }

    public function update(Request $request, Loan $loan): JsonResponse
    {
        $data = $request->validate([
            'status'  => 'sometimes|in:active,closed,default',
            'remarks' => 'nullable|string',
        ]);
        $loan->update($data);
        return response()->json($loan->fresh());
    }

    public function destroy(Loan $loan): JsonResponse
    {
        $loan->delete();
        return response()->json(['message' => 'Loan record deleted.']);
    }

    public function installmentSchedule(Loan $loan): JsonResponse
    {
        $schedule = $this->loanService->getInstallmentSchedule($loan);
        return response()->json([
            'loan'     => $loan->only(['id', 'loan_number', 'amount', 'interest_rate', 'installment_amount', 'outstanding_balance']),
            'schedule' => $schedule,
        ]);
    }

    public function recordRepayment(Request $request, Loan $loan): JsonResponse
    {
        $data = $request->validate([
            'amount_paid'       => 'required|numeric|min:0.01',
            'principal_paid'    => 'required|numeric|min:0',
            'interest_paid'     => 'required|numeric|min:0',
            'od_interest_paid'  => 'nullable|numeric|min:0',
            'penalty_paid'      => 'nullable|numeric|min:0',
            'payment_date'      => 'required|date',
            'transaction_mode'  => 'required|in:cash,cheque',
            'cheque_number'     => 'required_if:transaction_mode,cheque|nullable|string',
            'bank_name'         => 'required_if:transaction_mode,cheque|nullable|string',
            'cheque_date'       => 'required_if:transaction_mode,cheque|nullable|date',
            'installment_due_date' => 'nullable|date',
            'remarks'           => 'nullable|string',
        ]);

        $data['branch_id']  = $request->user()->branch_id;
        $data['created_by'] = $request->user()->id;

        $transaction = $this->loanService->recordRepayment($loan, $data);

        return response()->json($transaction, 201);
    }
}
