<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreFdAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id'        => 'required|exists:customers,id',
            'fd_setup_id'        => 'required|exists:fd_setups,id',
            'principal_amount'   => 'required|numeric|min:1000',
            'fd_date'            => 'required|date',
            'transaction_mode'   => 'required|in:cash,cheque',
        ];
    }
}
