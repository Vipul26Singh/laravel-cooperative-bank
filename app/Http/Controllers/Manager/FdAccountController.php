<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFdAccountRequest;
use App\Models\{FdAccount, FdSetup, Customer};
use App\Services\FdService;
use Illuminate\Support\Facades\Auth;

class FdAccountController extends Controller
{
    public function __construct(private FdService $fdService) {}

    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $fdAccounts = FdAccount::where('branch_id', $branchId)
            ->with(['customer', 'fdSetup'])
            ->latest()
            ->paginate(20);
        return view('manager.fd-accounts.index', compact('fdAccounts'));
    }

    public function create()
    {
        $branchId  = Auth::user()->branch_id;
        $fdSetups  = FdSetup::where('is_active', true)->get();
        $customers = Customer::where('branch_id', $branchId)->where('approval_status', 'approved')->get();
        return view('manager.fd-accounts.create', compact('fdSetups', 'customers'));
    }

    public function store(StoreFdAccountRequest $request)
    {
        $data = array_merge($request->validated(), [
            'branch_id'  => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        $fdAccount = $this->fdService->openFd($data);

        return redirect()->route('manager.fd-accounts.show', $fdAccount->id)
            ->with('success', 'FD account opened successfully.');
    }

    public function show(FdAccount $fdAccount)
    {
        $fdAccount->load(['customer', 'fdSetup']);
        return view('manager.fd-accounts.show', compact('fdAccount'));
    }
}
