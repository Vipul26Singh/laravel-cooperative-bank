<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\FdSetup;
use Illuminate\Http\Request;

class FdSetupController extends Controller
{
    public function index()
    {
        $fdSetups = FdSetup::latest()->paginate(20);
        return view('superadmin.fd-setups.index', compact('fdSetups'));
    }

    public function create()
    {
        return view('superadmin.fd-setups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'duration_months'   => 'required|integer|min:1',
            'interest_rate'     => 'required|numeric|min:0|max:100',
            'min_amount'        => 'nullable|numeric|min:0',
            'max_amount'        => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
        ]);

        FdSetup::create($request->only([
            'name', 'duration_months', 'interest_rate', 'min_amount', 'max_amount', 'description', 'is_active',
        ]));

        return redirect()->route('superadmin.fd-setups.index')
            ->with('success', 'FD setup created successfully.');
    }

    public function edit(FdSetup $fdSetup)
    {
        return view('superadmin.fd-setups.edit', compact('fdSetup'));
    }

    public function update(Request $request, FdSetup $fdSetup)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'interest_rate'   => 'required|numeric|min:0|max:100',
            'min_amount'      => 'nullable|numeric|min:0',
            'max_amount'      => 'nullable|numeric|min:0',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $fdSetup->update($request->only([
            'name', 'duration_months', 'interest_rate', 'min_amount', 'max_amount', 'description', 'is_active',
        ]));

        return redirect()->route('superadmin.fd-setups.index')
            ->with('success', 'FD setup updated successfully.');
    }

    public function destroy(FdSetup $fdSetup)
    {
        $fdSetup->update(['is_active' => false]);

        return redirect()->route('superadmin.fd-setups.index')
            ->with('success', 'FD setup deactivated successfully.');
    }
}
