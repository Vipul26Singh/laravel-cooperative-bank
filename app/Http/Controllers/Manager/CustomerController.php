<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService) {}

    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $query = Customer::where('branch_id', $branchId)->with(['city', 'state']);

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $customers = $query->latest()->paginate(20);
        return view('manager.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['city', 'state', 'country', 'branch']);
        return view('manager.customers.show', compact('customer'));
    }

    public function approve(Request $request, Customer $customer)
    {
        $this->customerService->approve($customer, Auth::user());

        return redirect()->route('manager.customers.show', $customer->id)
            ->with('success', 'Customer approved successfully.');
    }

    public function reject(Request $request, Customer $customer)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $this->customerService->reject($customer, Auth::user(), $request->rejection_reason);

        return redirect()->route('manager.customers.show', $customer->id)
            ->with('success', 'Customer rejected.');
    }
}
