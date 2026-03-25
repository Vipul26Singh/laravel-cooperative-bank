<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Models\{BankAccount, Customer, AccountType};
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function __construct(private AccountService $accountService) {}

    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $accounts = BankAccount::where('branch_id', $branchId)
            ->with(['customer', 'accountType'])
            ->latest()
            ->paginate(20);
        return view('manager.bank-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $branchId     = Auth::user()->branch_id;
        $customers    = Customer::where('branch_id', $branchId)->where('approval_status', 'approved')->get();
        $accountTypes = AccountType::where('is_active', true)->get();
        return view('manager.bank-accounts.create', compact('customers', 'accountTypes'));
    }

    public function store(StoreBankAccountRequest $request)
    {
        $data = array_merge($request->validated(), [
            'branch_id'  => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        $account = $this->accountService->openAccount($data);

        return redirect()->route('manager.bank-accounts.show', $account->id)
            ->with('success', 'Bank account opened successfully.');
    }

    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['customer', 'accountType', 'transactions']);
        return view('manager.bank-accounts.show', compact('bankAccount'));
    }
}
