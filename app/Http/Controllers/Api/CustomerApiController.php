<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function __construct(private CustomerService $customerService) {}

    public function index(Request $request): JsonResponse
    {
        $branchId  = $request->user()->branch_id;
        $customers = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->status, fn($q, $s) => $q->where('approval_status', $s))
            ->with(['city', 'state', 'branch'])
            ->latest()
            ->paginate(20);

        return response()->json($customers);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'dob'                 => 'nullable|date',
            'gender'              => 'required|in:Male,Female,Other',
            'mobile'              => 'required|string|max:20',
            'email'               => 'nullable|email',
            'residential_address' => 'required|string',
            'pincode'             => 'nullable|string|max:20',
            'city_id'             => 'nullable|exists:cities,id',
            'state_id'            => 'nullable|exists:states,id',
            'country_id'          => 'nullable|exists:countries,id',
            'pan_number'          => 'nullable|string|max:25',
            'uid_number'          => 'nullable|string|max:50',
            'nominee_name'        => 'nullable|string|max:100',
            'nominee_relation'    => 'nullable|string|max:100',
            'membership_fee'      => 'nullable|numeric|min:0',
        ]);

        $data['branch_id']  = $request->user()->branch_id;
        $data['created_by'] = $request->user()->id;

        $customer = $this->customerService->register($data);

        return response()->json($customer->load(['city', 'state', 'branch']), 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json($customer->load(['city', 'state', 'country', 'branch', 'bankAccounts', 'loans']));
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $data = $request->validate([
            'full_name'           => 'sometimes|string|max:255',
            'mobile'              => 'sometimes|string|max:20',
            'email'               => 'nullable|email',
            'residential_address' => 'sometimes|string',
        ]);

        $data['modified_by'] = $request->user()->id;
        $customer->update($data);

        return response()->json($customer->fresh());
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted.']);
    }

    public function approve(Request $request, Customer $customer): JsonResponse
    {
        $request->validate(['remark' => 'nullable|string']);
        $customer = $this->customerService->approve($customer, $request->user()->id, $request->remark ?? '');
        return response()->json($customer);
    }

    public function reject(Request $request, Customer $customer): JsonResponse
    {
        $request->validate(['remark' => 'required|string']);
        $customer = $this->customerService->reject($customer, $request->user()->id, $request->remark);
        return response()->json($customer);
    }
}
