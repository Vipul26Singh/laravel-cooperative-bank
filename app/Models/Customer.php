<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_number',
        'full_name',
        'dob',
        'age',
        'gender',
        'marital_status',
        'spouse_name',
        'spouse_dob',
        'residential_address',
        'office_address',
        'pincode',
        'phone',
        'mobile',
        'email',
        'city_id',
        'state_id',
        'country_id',
        'father_name',
        'family_details',
        'family_relation',
        'nominee_name',
        'nominee_age',
        'nominee_relation',
        'religion',
        'caste',
        'pan_number',
        'uid_number',
        'id_type',
        'photo_identity_number',
        'photo',
        'photo_id',
        'id_proof1',
        'id_proof2',
        'signature',
        'membership_fee',
        'branch_id',
        'is_member_active',
        'activation_date',
        'approval_status',
        'approval_date',
        'approver_remark',
        'approved_by',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'dob'              => 'date',
        'spouse_dob'       => 'date',
        'activation_date'  => 'date',
        'approval_date'    => 'datetime',
        'is_member_active' => 'boolean',
        'membership_fee'   => 'decimal:2',
        'approval_status'  => 'string',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function shareAccount(): HasOne
    {
        return $this->hasOne(ShareAccount::class);
    }

    public function fdAccounts(): HasMany
    {
        return $this->hasMany(FdAccount::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeByBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }
}
