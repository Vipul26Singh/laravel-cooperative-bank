<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    protected $fillable = [
        'name',
        'interest_rate',
        'minimum_balance',
        'interest_calculation_days',
        'type',
        'is_active',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'interest_rate'   => 'decimal:4',
        'minimum_balance' => 'decimal:2',
    ];

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }
}
