<?php
namespace App\Http\Controllers\Cashier;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankTransactionRequest;
use App\Models\BankAccount;
use App\Services\AccountService;

class BankTransactionController extends Controller
{
    public function __construct(private AccountService $accountService) {}

    public function index()
    {
        $branchId = auth()->user()->branch_id;
        $transactions = \App\Models\BankAccountTransaction::where('branch_id', $branchId)
            ->with(['bankAccount', 'customer'])
            ->latest('transaction_date')
            ->paginate(20);
        return view('cashier.transactions.index', compact('transactions'));
    }

    public function create()
    {
        return view('cashier.transactions.create');
    }

    public function store(StoreBankTransactionRequest $request)
    {
        $account = BankAccount::where('account_number', $request->account_number)
            ->where('branch_id', auth()->user()->branch_id)
            ->firstOrFail();

        $data = array_merge($request->validated(), [
            'branch_id'        => auth()->user()->branch_id,
            'created_by'       => auth()->id(),
            'transaction_date' => now(),
        ]);

        if ($request->transaction_type === 'Deposit') {
            $tx = $this->accountService->deposit($account, $data);
        } else {
            $tx = $this->accountService->withdraw($account, $data);
        }

        return redirect()->route('cashier.transactions.show', $tx->id)
            ->with('success', 'Transaction recorded successfully');
    }

    public function show(\App\Models\BankAccountTransaction $transaction)
    {
        return view('cashier.transactions.show', compact('transaction'));
    }
}
