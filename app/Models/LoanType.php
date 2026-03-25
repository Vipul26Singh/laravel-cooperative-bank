<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'interest_rate',
        'duration_months',
        'max_amount',
        'num_installments',
        'frequency',
        'is_active',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'interest_rate' => 'decimal:4',
        'max_amount'    => 'decimal:2',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }
}
