<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{BankAccount, BankAccountTransaction};
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionApiController extends Controller
{
    public function __construct(private AccountService $accountService) {}

    public function index(Request $request): JsonResponse
    {
        $transactions = BankAccountTransaction::where('branch_id', $request->user()->branch_id)
            ->when($request->account_number, fn($q, $n) => $q->where('account_number', $n))
            ->when($request->from_date, fn($q, $d) => $q->whereDate('transaction_date', '>=', $d))
            ->when($request->to_date, fn($q, $d) => $q->whereDate('transaction_date', '<=', $d))
            ->with(['customer'])
            ->latest('transaction_date')
            ->paginate(50);

        return response()->json($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'account_number'   => 'required|integer',
            'transaction_type' => 'required|in:Deposit,Withdraw',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_mode' => 'required|in:cash,cheque',
            'cheque_number'    => 'required_if:transaction_mode,cheque|nullable|string',
            'bank_name'        => 'required_if:transaction_mode,cheque|nullable|string',
            'cheque_date'      => 'required_if:transaction_mode,cheque|nullable|date',
            'remarks'          => 'nullable|string|max:255',
        ]);

        $account = BankAccount::where('account_number', $data['account_number'])
            ->where('is_active', true)
            ->firstOrFail();

        $txData = array_merge($data, [
            'branch_id'        => $request->user()->branch_id,
            'created_by'       => $request->user()->id,
            'transaction_date' => now(),
        ]);

        $tx = $data['transaction_type'] === 'Deposit'
            ? $this->accountService->deposit($account, $txData)
            : $this->accountService->withdraw($account, $txData);

        return response()->json($tx->load('customer'), 201);
    }

    public function show(BankAccountTransaction $transaction): JsonResponse
    {
        return response()->json($transaction->load(['bankAccount', 'customer']));
    }
}
