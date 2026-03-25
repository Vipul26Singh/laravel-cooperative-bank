<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FdSetup extends Model
{
    protected $fillable = [
        'description',
        'interest_rate',
        'duration_days',
        'is_senior_citizen',
        'is_special_roi',
        'is_active',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'is_senior_citizen' => 'boolean',
        'is_special_roi'    => 'boolean',
        'is_active'         => 'boolean',
        'interest_rate'     => 'decimal:4',
    ];

    public function fdAccounts(): HasMany
    {
        return $this->hasMany(FdAccount::class);
    }
}
