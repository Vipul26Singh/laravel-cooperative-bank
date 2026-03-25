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
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'gst_no'  => 'nullable|string|max:50',
            'pan_no'  => 'nullable|string|max:50',
            'logo'    => 'nullable|image|max:2048',
        ]);

        $company = CompanySetup::first() ?? new CompanySetup();

        $data = $request->only([
            'name', 'address', 'phone', 'email', 'website', 'gst_no', 'pan_no',
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
