<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InitializeAccountNumber extends Model
{
    protected $fillable = [
        'branch_id',
        'bank_account_start',
        'fd_account_start',
        'share_account_start',
        'loan_account_start',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'bank_account_start'  => 'integer',
        'fd_account_start'    => 'integer',
        'share_account_start' => 'integer',
        'loan_account_start'  => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
