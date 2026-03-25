<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(20);
        return view('superadmin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('superadmin.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:105',
            'code'         => 'required|string|max:50|unique:branches,code',
            'address'      => 'nullable|string',
            'opening_date' => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        Branch::create($request->only([
            'name', 'code', 'address', 'opening_date', 'is_active',
        ]));

        return redirect()->route('superadmin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch)
    {
        return view('superadmin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name'         => 'required|string|max:105',
            'code'         => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'address'      => 'nullable|string',
            'opening_date' => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        $branch->update($request->only([
            'name', 'code', 'address', 'opening_date', 'is_active',
        ]));

        return redirect()->route('superadmin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->update(['is_active' => false]);

        return redirect()->route('superadmin.branches.index')
            ->with('success', 'Branch deactivated successfully.');
    }
}
