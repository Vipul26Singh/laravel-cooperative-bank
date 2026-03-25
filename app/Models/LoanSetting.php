<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSetting extends Model
{
    protected $fillable = [
        'grace_days',
        'penalty_rate',
        'od_interest_rate',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'penalty_rate'    => 'decimal:4',
        'od_interest_rate' => 'decimal:4',
    ];

    /**
     * Get the current loan settings (first record).
     */
    public static function current(): ?self
    {
        return static::first();
    }
}
