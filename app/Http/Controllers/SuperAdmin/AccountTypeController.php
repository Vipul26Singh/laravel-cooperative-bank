<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    public function index()
    {
        $accountTypes = AccountType::latest()->paginate(20);
        return view('superadmin.account-types.index', compact('accountTypes'));
    }

    public function create()
    {
        return view('superadmin.account-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                      => 'required|string|max:50',
            'type'                      => 'required|string|max:50',
            'minimum_balance'           => 'nullable|numeric|min:0',
            'interest_rate'             => 'nullable|numeric|min:0|max:100',
            'interest_calculation_days' => 'nullable|integer|min:1',
            'is_active'                 => 'boolean',
        ]);

        AccountType::create($request->only([
            'name', 'type', 'minimum_balance', 'interest_rate',
            'interest_calculation_days', 'is_active',
        ]));

        return redirect()->route('superadmin.account-types.index')
            ->with('success', 'Account type created successfully.');
    }

    public function edit(AccountType $accountType)
    {
        return view('superadmin.account-types.edit', compact('accountType'));
    }

    public function update(Request $request, AccountType $accountType)
    {
        $request->validate([
            'name'                      => 'required|string|max:50',
            'type'                      => 'required|string|max:50',
            'minimum_balance'           => 'nullable|numeric|min:0',
            'interest_rate'             => 'nullable|numeric|min:0|max:100',
            'interest_calculation_days' => 'nullable|integer|min:1',
            'is_active'                 => 'boolean',
        ]);

        $accountType->update($request->only([
            'name', 'type', 'minimum_balance', 'interest_rate',
            'interest_calculation_days', 'is_active',
        ]));

        return redirect()->route('superadmin.account-types.index')
            ->with('success', 'Account type updated successfully.');
    }

    public function destroy(AccountType $accountType)
    {
        $accountType->update(['is_active' => false]);

        return redirect()->route('superadmin.account-types.index')
            ->with('success', 'Account type deactivated successfully.');
    }
}
