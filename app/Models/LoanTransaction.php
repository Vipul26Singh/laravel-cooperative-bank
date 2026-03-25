<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTransaction extends Model
{
    protected $fillable = [
        'loan_id',
        'loan_number',
        'customer_id',
        'amount_paid',
        'principal_paid',
        'interest_paid',
        'od_interest_paid',
        'penalty_paid',
        'outstanding_balance_after',
        'payment_date',
        'installment_due_date',
        'transaction_mode',
        'cheque_number',
        'bank_name',
        'cheque_date',
        'sender_receiver_account',
        'remarks',
        'branch_id',
        'via_mobile',
        'via_internet',
        'status',
        'approved_by',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'amount_paid'              => 'decimal:2',
        'principal_paid'           => 'decimal:2',
        'interest_paid'            => 'decimal:2',
        'od_interest_paid'         => 'decimal:2',
        'penalty_paid'             => 'decimal:2',
        'outstanding_balance_after' => 'decimal:2',
        'payment_date'             => 'date',
        'installment_due_date'     => 'date',
        'cheque_date'              => 'date',
        'via_mobile'               => 'boolean',
        'via_internet'             => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
