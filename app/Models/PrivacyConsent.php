<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visitor_id',
        'recorded_by_user_id',
        'purpose',
        'lawful_basis',
        'notice_version',
        'consent_required',
        'granted',
        'acknowledged_at',
        'granted_at',
        'withdrawn_at',
        'source',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'consent_required' => 'boolean',
            'granted' => 'boolean',
            'acknowledged_at' => 'datetime',
            'granted_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by_user_id'
        );
    }
}
