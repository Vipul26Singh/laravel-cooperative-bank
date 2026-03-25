<?php
namespace App\Http\Controllers\Clerk;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService) {}

    public function index()
    {
        $customers = Customer::where('created_by', Auth::id())
            ->with(['city', 'state', 'branch'])
            ->latest()
            ->paginate(20);

        return view('clerk.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('clerk.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $data['branch_id']  = Auth::user()->branch_id;
        $data['created_by'] = Auth::id();

        // Handle file uploads as base64
        foreach (['photo', 'id_proof1', 'id_proof2'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = base64_encode(
                    file_get_contents($request->file($field)->getRealPath())
                );
            }
        }

        $customer = $this->customerService->register($data);

        return redirect()->route('clerk.customers.show', $customer->id)
            ->with('success', 'Customer registered successfully. Pending approval.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['city', 'state', 'country', 'branch']);
        return view('clerk.customers.show', compact('customer'));
    }
}
