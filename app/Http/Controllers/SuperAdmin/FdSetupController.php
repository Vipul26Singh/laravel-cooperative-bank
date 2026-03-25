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
            'description'       => 'required|string',
            'interest_rate'     => 'required|numeric|min:0|max:100',
            'duration_days'     => 'required|integer|min:1',
            'is_senior_citizen' => 'boolean',
            'is_special_roi'    => 'boolean',
            'is_active'         => 'boolean',
        ]);

        FdSetup::create($request->only([
            'description', 'interest_rate', 'duration_days',
            'is_senior_citizen', 'is_special_roi', 'is_active',
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
            'description'       => 'required|string',
            'interest_rate'     => 'required|numeric|min:0|max:100',
            'duration_days'     => 'required|integer|min:1',
            'is_senior_citizen' => 'boolean',
            'is_special_roi'    => 'boolean',
            'is_active'         => 'boolean',
        ]);

        $fdSetup->update($request->only([
            'description', 'interest_rate', 'duration_days',
            'is_senior_citizen', 'is_special_roi', 'is_active',
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
