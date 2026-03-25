<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldLoanType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'interest_rate',
        'duration_months',
        'max_amount',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'interest_rate' => 'decimal:4',
        'max_amount'    => 'decimal:2',
    ];

    public function goldLoanApplications(): HasMany
    {
        return $this->hasMany(GoldLoanApplication::class);
    }
}
