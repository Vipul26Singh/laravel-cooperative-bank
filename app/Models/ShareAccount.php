<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShareAccount extends Model
{
    protected $fillable = [
        'share_account_number',
        'customer_id',
        'balance_shares',
        'opening_date',
        'branch_id',
        'is_active',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'balance_shares' => 'decimal:4',
        'is_active'      => 'boolean',
        'opening_date'   => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ShareTransaction::class);
    }
}
