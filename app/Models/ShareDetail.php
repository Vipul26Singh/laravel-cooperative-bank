<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareDetail extends Model
{
    protected $fillable = [
        'price_per_share',
        'effective_date',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'price_per_share' => 'decimal:4',
        'effective_date'  => 'date',
    ];

    /**
     * Get the current (latest) share price.
     */
    public static function getCurrentPrice(): ?self
    {
        return static::orderByDesc('effective_date')->first();
    }
}
