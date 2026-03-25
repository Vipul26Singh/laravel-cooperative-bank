<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'bank_account_id'    => 'required|exists:bank_accounts,id',
            'transaction_type'   => 'required|in:Deposit,Withdraw',
            'amount'             => 'required|numeric|min:0.01',
            'transaction_mode'   => 'required|in:cash,cheque',
            'cheque_number'      => 'required_if:transaction_mode,cheque|nullable|string|max:50',
            'bank_name'          => 'required_if:transaction_mode,cheque|nullable|string|max:255',
            'cheque_date'        => 'required_if:transaction_mode,cheque|nullable|date',
        ];
    }
}
