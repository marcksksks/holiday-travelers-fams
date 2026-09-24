<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyRequest extends Model
{
    use HasFactory;

    public const TYPE_ERASURE = 'erasure';

    public const TYPE_BLOCKING = 'blocking';

    public const TYPE_WITHDRAW_CONSENT =
        'withdraw_consent';

    public const TYPES = [
        self::TYPE_ERASURE,
        self::TYPE_BLOCKING,
        self::TYPE_WITHDRAW_CONSENT,
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_UNDER_REVIEW =
        'under_review';

    public const STATUS_APPROVED =
        'approved';

    public const STATUS_PARTIALLY_APPROVED =
        'partially_approved';

    public const STATUS_DENIED =
        'denied';

    public const STATUS_COMPLETED =
        'completed';

    public const OPEN_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_PARTIALLY_APPROVED,
    ];

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'details',
        'identity_verified_at',
        'submitted_at',
        'reviewed_by_user_id',
        'reviewed_at',
        'decision_reason',
        'retention_basis',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'identity_verified_at' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by_user_id'
        );
    }
}
