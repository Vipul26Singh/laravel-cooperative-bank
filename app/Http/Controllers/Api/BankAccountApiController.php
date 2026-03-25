<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountApiController extends Controller
{
    public function __construct(private AccountService $accountService) {}

    public function index(Request $request): JsonResponse
    {
        $accounts = BankAccount::where('branch_id', $request->user()->branch_id)
            ->with(['customer', 'accountType'])
            ->paginate(20);

        return response()->json($accounts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'account_type_id' => 'required|exists:account_types,id',
            'opening_date'    => 'required|date',
            'balance'         => 'nullable|numeric|min:0',
        ]);

        $data['branch_id']  = $request->user()->branch_id;
        $data['created_by'] = $request->user()->id;

        $account = $this->accountService->openAccount($data);

        return response()->json($account->load(['customer', 'accountType']), 201);
    }

    public function show(BankAccount $bankAccount): JsonResponse
    {
        return response()->json($bankAccount->load(['customer', 'accountType', 'transactions']));
    }

    public function update(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $data = $request->validate([
            'is_active'   => 'sometimes|boolean',
            'modified_by' => 'nullable',
        ]);
        $data['modified_by'] = $request->user()->id;
        $bankAccount->update($data);

        return response()->json($bankAccount->fresh());
    }

    public function destroy(BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->delete();
        return response()->json(['message' => 'Account closed.']);
    }

    public function findByNumber(int $accountNumber): JsonResponse
    {
        $account = BankAccount::where('account_number', $accountNumber)
            ->where('is_active', true)
            ->with(['customer', 'accountType'])
            ->firstOrFail();

        return response()->json($account);
    }
}
