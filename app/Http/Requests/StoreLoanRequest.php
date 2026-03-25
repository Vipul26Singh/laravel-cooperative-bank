<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'loan_application_id'      => 'nullable|exists:loan_applications,id',
            'customer_id'              => 'required|exists:customers,id',
            'loan_type_id'             => 'required|exists:loan_types,id',
            'amount'                   => 'required|numeric|min:0',
            'interest_rate'            => 'required|numeric|min:0|max:100',
            'duration_months'          => 'required|integer|min:1',
            'first_installment_date'   => 'required|date',
            'frequency'                => 'required|in:DAILY,WEEKLY,MONTHLY',
        ];
    }
}
