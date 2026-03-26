<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledTask extends Model
{
    protected $fillable = [
        'name',
        'command',
        'frequency',
        'description',
        'is_active',
        'last_run_at',
        'next_run_at',
        'last_status',
        'last_output',
        'run_count',
        'fail_count',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(TaskRunLog::class);
    }

    /**
     * Map frequency string to human-readable label.
     */
    public function getFrequencyLabelAttribute(): string
    {
        return match (true) {
            str_starts_with($this->frequency, 'dailyAt:')    => 'Daily at ' . str_replace('dailyAt:', '', $this->frequency),
            $this->frequency === 'daily'                      => 'Daily at midnight',
            $this->frequency === 'hourly'                     => 'Every hour',
            $this->frequency === 'everyFiveMinutes'           => 'Every 5 minutes',
            $this->frequency === 'everyTenMinutes'            => 'Every 10 minutes',
            $this->frequency === 'everyFifteenMinutes'        => 'Every 15 minutes',
            $this->frequency === 'everyThirtyMinutes'         => 'Every 30 minutes',
            $this->frequency === 'weekly'                     => 'Weekly',
            $this->frequency === 'monthly'                    => 'Monthly',
            default                                           => $this->frequency,
        };
    }
}
