<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoldLoanApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'application_number',
        'customer_id',
        'gold_loan_type_id',
        'branch_id',
        'gold_weight',
        'gold_purity',
        'gold_value',
        'applied_amount',
        'purpose',
        'duration_months',
        'application_date',
        'status',
        'approved_by',
        'approved_at',
        'approved_amount',
        'rejection_reason',
        'remarks',
        'via_mobile',
        'via_internet',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'gold_weight'      => 'decimal:4',
        'gold_purity'      => 'decimal:4',
        'gold_value'       => 'decimal:2',
        'applied_amount'   => 'decimal:2',
        'approved_amount'  => 'decimal:2',
        'application_date' => 'date',
        'approved_at'      => 'datetime',
        'via_mobile'       => 'boolean',
        'via_internet'     => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(GoldLoanType::class, 'gold_loan_type_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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
