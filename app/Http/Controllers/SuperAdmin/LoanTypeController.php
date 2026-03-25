<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\LoanType;
use Illuminate\Http\Request;

class LoanTypeController extends Controller
{
    public function index()
    {
        $loanTypes = LoanType::latest()->paginate(20);
        return view('superadmin.loan-types.index', compact('loanTypes'));
    }

    public function create()
    {
        return view('superadmin.loan-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'min_amount'        => 'nullable|numeric|min:0',
            'max_amount'        => 'nullable|numeric|min:0',
            'interest_rate'     => 'required|numeric|min:0|max:100',
            'max_duration'      => 'nullable|integer|min:1',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
        ]);

        LoanType::create($request->only([
            'name', 'min_amount', 'max_amount', 'interest_rate', 'max_duration', 'description', 'is_active',
        ]));

        return redirect()->route('superadmin.loan-types.index')
            ->with('success', 'Loan type created successfully.');
    }

    public function edit(LoanType $loanType)
    {
        return view('superadmin.loan-types.edit', compact('loanType'));
    }

    public function update(Request $request, LoanType $loanType)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'min_amount'    => 'nullable|numeric|min:0',
            'max_amount'    => 'nullable|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'max_duration'  => 'nullable|integer|min:1',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $loanType->update($request->only([
            'name', 'min_amount', 'max_amount', 'interest_rate', 'max_duration', 'description', 'is_active',
        ]));

        return redirect()->route('superadmin.loan-types.index')
            ->with('success', 'Loan type updated successfully.');
    }

    public function destroy(LoanType $loanType)
    {
        $loanType->update(['is_active' => false]);

        return redirect()->route('superadmin.loan-types.index')
            ->with('success', 'Loan type deactivated successfully.');
    }
}
