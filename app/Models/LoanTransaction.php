<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTransaction extends Model
{
    protected $fillable = [
        'loan_id',
        'customer_id',
        'branch_id',
        'transaction_type',
        'amount',
        'principal_component',
        'interest_component',
        'penalty_amount',
        'outstanding_after',
        'transaction_date',
        'due_date',
        'reference_number',
        'narration',
        'via_mobile',
        'via_internet',
        'approved_by',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'amount'              => 'decimal:2',
        'principal_component' => 'decimal:2',
        'interest_component'  => 'decimal:2',
        'penalty_amount'      => 'decimal:2',
        'outstanding_after'   => 'decimal:2',
        'transaction_date'    => 'date',
        'due_date'            => 'date',
        'via_mobile'          => 'boolean',
        'via_internet'        => 'boolean',
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
