<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'            => 'required|string|max:255',
            'dob'                  => 'nullable|date|before:today',
            'gender'               => 'required|in:Male,Female,Other',
            'mobile'               => 'required|string|max:20',
            'email'                => 'nullable|email|max:255',
            'residential_address'  => 'required|string',
            'pincode'              => 'nullable|string|max:20',
            'city_id'              => 'nullable|exists:cities,id',
            'state_id'             => 'nullable|exists:states,id',
            'country_id'           => 'nullable|exists:countries,id',
            'pan_number'           => 'nullable|string|max:25',
            'uid_number'           => 'nullable|string|max:50',
            'nominee_name'         => 'nullable|string|max:100',
            'nominee_relation'     => 'nullable|string|max:100',
            'photo'                => 'nullable|image|max:2048',
            'id_proof1'            => 'nullable|file|max:2048',
            'id_proof2'            => 'nullable|file|max:2048',
            'membership_fee'       => 'nullable|numeric|min:0',
        ];
    }
}
