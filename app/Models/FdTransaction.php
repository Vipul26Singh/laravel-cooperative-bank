<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FdTransaction extends Model
{
    protected $fillable = [
        'fd_account_id',
        'fd_number',
        'customer_id',
        'transaction_type',
        'amount',
        'interest_earned',
        'balance_after',
        'transaction_date',
        'remarks',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'interest_earned'  => 'decimal:2',
        'balance_after'    => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function fdAccount(): BelongsTo
    {
        return $this->belongsTo(FdAccount::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
