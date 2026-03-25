<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\CompanySetup;
use Illuminate\Http\Request;

class CompanySetupController extends Controller
{
    public function show()
    {
        $company = CompanySetup::first() ?? new CompanySetup();
        return view('superadmin.company-setup.show', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name'      => 'required|string|max:255',
            'registration_no'   => 'nullable|string|max:100',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'state'             => 'nullable|string|max:100',
            'country'           => 'nullable|string|max:100',
            'pincode'           => 'nullable|string|max:20',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'website'           => 'nullable|url|max:255',
            'logo'              => 'nullable|image|max:2048',
            'established_year'  => 'nullable|integer|min:1800|max:' . now()->year,
        ]);

        $company = CompanySetup::first() ?? new CompanySetup();

        $data = $request->only([
            'company_name', 'registration_no', 'address', 'city', 'state',
            'country', 'pincode', 'phone', 'email', 'website', 'established_year',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = base64_encode(file_get_contents($request->file('logo')->getRealPath()));
        }

        if ($company->exists) {
            $company->update($data);
        } else {
            CompanySetup::create($data);
        }

        return redirect()->route('superadmin.company-setup.show')
            ->with('success', 'Company settings saved successfully.');
    }
}
