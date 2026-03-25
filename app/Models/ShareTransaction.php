<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareTransaction extends Model
{
    protected $fillable = [
        'share_account_id',
        'customer_id',
        'branch_id',
        'transaction_type',
        'shares_count',
        'share_amount',
        'balance_shares_after',
        'transaction_date',
        'reference_number',
        'narration',
        'via_mobile',
        'via_internet',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'shares_count'        => 'decimal:4',
        'share_amount'        => 'decimal:2',
        'balance_shares_after' => 'decimal:4',
        'transaction_date'    => 'date',
        'via_mobile'          => 'boolean',
        'via_internet'        => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function shareAccount(): BelongsTo
    {
        return $this->belongsTo(ShareAccount::class);
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
