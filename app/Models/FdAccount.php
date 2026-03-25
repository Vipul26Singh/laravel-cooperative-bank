<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class FdAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fd_number',
        'customer_id',
        'fd_setup_id',
        'branch_id',
        'principal_amount',
        'interest_rate',
        'duration_days',
        'fd_date',
        'maturity_amount',
        'maturity_date',
        'withdrawal_date',
        'is_matured',
        'is_withdrawn',
        'transaction_mode',
        'cheque_number',
        'bank_name',
        'cheque_date',
        'via_mobile',
        'via_internet',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate'    => 'decimal:4',
        'maturity_amount'  => 'decimal:2',
        'fd_date'          => 'datetime',
        'maturity_date'    => 'date',
        'is_matured'       => 'boolean',
        'is_withdrawn'     => 'boolean',
        'via_mobile'       => 'boolean',
        'via_internet'     => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function fdSetup(): BelongsTo
    {
        return $this->belongsTo(FdSetup::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FdTransaction::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Check if the FD has reached its maturity date.
     */
    public function isMatured(): bool
    {
        return $this->maturity_date !== null && Carbon::now()->gte($this->maturity_date);
    }

    /**
     * Compute the maturity amount using simple interest.
     * Formula: P * (1 + (r/100) * (days/365))
     *
     * @param  float  $principal
     * @param  float  $annualRate   Annual interest rate in percent (e.g. 7.5)
     * @param  int    $days         Duration in days
     */
    public static function computeMaturityAmount(float $principal, float $annualRate, int $days): float
    {
        return $principal * (1 + ($annualRate / 100) * ($days / 365));
    }
}
