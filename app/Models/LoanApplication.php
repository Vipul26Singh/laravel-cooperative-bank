<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'loan_type_id',
        'applied_amount',
        'approved_amount',
        'application_date',
        'loan_purpose',
        'duration_months',
        'frequency',
        'guarantor1_id',
        'guarantor2_id',
        'remarks',
        'approval_status',
        'approval_date',
        'approver_remark',
        'approved_by',
        'loan_status',
        'branch_id',
        'via_mobile',
        'via_internet',
        'created_by',
    ];

    protected $casts = [
        'applied_amount'   => 'decimal:2',
        'approved_amount'  => 'decimal:2',
        'application_date' => 'date',
        'approval_date'    => 'datetime',
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
        return $this->belongsTo(LoanType::class);
    }

    public function guarantor1(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'guarantor1_id');
    }

    public function guarantor2(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'guarantor2_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class, 'loan_application_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', 'approved');
    }
}
