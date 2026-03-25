<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id'      => 'required|exists:customers,id',
            'account_type_id'  => 'required|exists:account_types,id',
            'opening_date'     => 'required|date',
            'balance'          => 'nullable|numeric|min:0',
        ];
    }
}
