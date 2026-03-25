<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_number',
        'loan_application_id',
        'customer_id',
        'loan_type_id',
        'branch_id',
        'amount',
        'interest_rate',
        'duration_months',
        'num_installments',
        'installment_amount',
        'frequency',
        'outstanding_balance',
        'first_installment_date',
        'disburse_date',
        'loan_date',
        'guarantor1_id',
        'guarantor2_id',
        'collector_id',
        'type',
        'fd_account_id',
        'od_interest_amount',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'amount'               => 'decimal:2',
        'interest_rate'        => 'decimal:4',
        'installment_amount'   => 'decimal:2',
        'outstanding_balance'  => 'decimal:2',
        'od_interest_amount'   => 'decimal:2',
        'first_installment_date' => 'date',
        'disburse_date'        => 'datetime',
        'loan_date'            => 'date',
        'status'               => 'string',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function guarantor1(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'guarantor1_id');
    }

    public function guarantor2(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'guarantor2_id');
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function fdAccount(): BelongsTo
    {
        return $this->belongsTo(FdAccount::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('status', 'default');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Calculate the monthly EMI using the standard formula:
     * EMI = P * r * (1 + r)^n / ((1 + r)^n - 1)
     * where r = annualRate / 12 / 100
     *
     * @param  float  $principal    Loan principal amount
     * @param  float  $annualRate   Annual interest rate in percent (e.g. 12.0)
     * @param  int    $months       Loan duration in months
     */
    public static function calculateEmi(float $principal, float $annualRate, int $months): float
    {
        if ($annualRate == 0) {
            return $months > 0 ? $principal / $months : 0;
        }

        $r = $annualRate / 12 / 100;
        $n = $months;

        return ($principal * $r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);
    }
}
