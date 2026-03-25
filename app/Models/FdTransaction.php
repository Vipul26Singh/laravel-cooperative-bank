<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FdTransaction extends Model
{
    protected $fillable = [
        'fd_account_id',
        'customer_id',
        'branch_id',
        'transaction_type',
        'amount',
        'interest_amount',
        'transaction_date',
        'reference_number',
        'narration',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'interest_amount'  => 'decimal:2',
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
