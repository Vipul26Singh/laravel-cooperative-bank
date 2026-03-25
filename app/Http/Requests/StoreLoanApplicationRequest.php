<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id'      => 'required|exists:customers,id',
            'loan_type_id'     => 'required|exists:loan_types,id',
            'applied_amount'   => 'required|numeric|min:1000',
            'duration_months'  => 'required|integer|min:1|max:360',
            'loan_purpose'     => 'required|string',
            'frequency'        => 'required|in:DAILY,WEEKLY,MONTHLY',
            'guarantor1_id'    => 'nullable|exists:customers,id',
            'guarantor2_id'    => 'nullable|exists:customers,id',
        ];
    }
}
