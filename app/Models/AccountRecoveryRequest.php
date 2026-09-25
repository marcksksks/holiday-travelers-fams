<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountRecoveryRequest extends Model
{
    public const STATUS_PENDING =
        'pending';

    public const STATUS_APPROVED =
        'approved';

    public const STATUS_REJECTED =
        'rejected';

    public const STATUS_COMPLETED =
        'completed';

    public const STATUS_EXPIRED =
        'expired';

    public const METHOD_ADMIN =
        'admin';

    public const METHOD_RECOVERY_CODE =
        'recovery_code';

    protected $fillable = [
        'user_id',
        'reference',
        'claim_token_hash',
        'status',
        'recovery_method',
        'approved_by',
        'request_ip',
        'user_agent',
        'requested_at',
        'approved_at',
        'rejected_at',
        'completed_at',
        'expires_at',
    ];

    protected $hidden = [
        'claim_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'request_ip' => 'encrypted',
            'user_agent' => 'encrypted',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status ===
            self::STATUS_APPROVED;
    }

    public function hasExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function canReset(): bool
    {
        return $this->isApproved()
            && ! $this->hasExpired();
    }
}
