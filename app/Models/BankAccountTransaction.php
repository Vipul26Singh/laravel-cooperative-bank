<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccountTransaction extends Model
{
    protected $fillable = [
        'bank_account_id',
        'account_number',
        'customer_id',
        'transaction_type',
        'amount',
        'balance_after',
        'transaction_mode',
        'cheque_number',
        'bank_name',
        'cheque_date',
        'sender_receiver_account',
        'transaction_date',
        'remarks',
        'branch_id',
        'via_mobile',
        'via_internet',
        'otp_confirmed',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'balance_after'    => 'decimal:2',
        'transaction_date' => 'datetime',
        'cheque_date'      => 'date',
        'via_mobile'       => 'boolean',
        'via_internet'     => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
