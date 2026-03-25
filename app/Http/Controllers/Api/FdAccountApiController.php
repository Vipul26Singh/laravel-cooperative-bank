<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FdAccount;
use App\Services\FdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FdAccountApiController extends Controller
{
    public function __construct(private FdService $fdService) {}

    public function index(Request $request): JsonResponse
    {
        $fds = FdAccount::where('branch_id', $request->user()->branch_id)
            ->when($request->customer_id, fn($q, $c) => $q->where('customer_id', $c))
            ->when($request->is_matured !== null, fn($q) => $q->where('is_matured', $request->boolean('is_matured')))
            ->with(['customer', 'fdSetup'])
            ->paginate(20);

        return response()->json($fds);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'fd_setup_id'      => 'required|exists:fd_setups,id',
            'principal_amount' => 'required|numeric|min:1000',
            'fd_date'          => 'required|date',
            'transaction_mode' => 'required|in:cash,cheque',
            'cheque_number'    => 'required_if:transaction_mode,cheque|nullable|string',
            'bank_name'        => 'required_if:transaction_mode,cheque|nullable|string',
            'cheque_date'      => 'required_if:transaction_mode,cheque|nullable|date',
        ]);

        // Load FD setup for rate and duration
        $fdSetup = \App\Models\FdSetup::findOrFail($data['fd_setup_id']);
        $data['interest_rate'] = $fdSetup->interest_rate;
        $data['duration_days'] = $fdSetup->duration_days;
        $data['branch_id']     = $request->user()->branch_id;
        $data['created_by']    = $request->user()->id;

        $fd = $this->fdService->openFd($data);

        return response()->json($fd->load(['customer', 'fdSetup']), 201);
    }

    public function show(FdAccount $fdAccount): JsonResponse
    {
        return response()->json($fdAccount->load(['customer', 'fdSetup', 'transactions']));
    }

    public function update(Request $request, FdAccount $fdAccount): JsonResponse
    {
        $data = $request->validate([
            'is_withdrawn'    => 'sometimes|boolean',
            'withdrawal_date' => 'sometimes|date',
        ]);
        $data['modified_by'] = $request->user()->id;
        $fdAccount->update($data);
        return response()->json($fdAccount->fresh());
    }

    public function destroy(FdAccount $fdAccount): JsonResponse
    {
        $fdAccount->delete();
        return response()->json(['message' => 'FD account deleted.']);
    }
}
