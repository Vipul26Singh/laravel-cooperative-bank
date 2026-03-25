<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'branch_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Create an audit log entry.
     *
     * @param  string      $action   e.g. 'created', 'updated', 'deleted'
     * @param  Model|null  $model    The affected Eloquent model instance
     * @param  array       $old      Old attribute values (before change)
     * @param  array       $new      New attribute values (after change)
     */
    public static function log(
        string $action,
        ?Model $model = null,
        array $old = [],
        array $new = []
    ): self {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return static::create([
            'user_id'    => $user?->id,
            'action'     => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'branch_id'  => $user?->branch_id,
        ]);
    }
}
