<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRepaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'loan_id'           => 'required|exists:loans,id',
            'amount_paid'       => 'required|numeric|min:0.01',
            'principal_paid'    => 'required|numeric|min:0',
            'interest_paid'     => 'required|numeric|min:0',
            'payment_date'      => 'required|date',
            'transaction_mode'  => 'required|in:cash,cheque',
        ];
    }
}
