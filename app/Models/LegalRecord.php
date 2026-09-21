<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalRecord extends Model
{
    use HasFactory;

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
        'critical',
    ];

    public const CONFIDENTIALITY_LEVELS = [
        'internal',
        'confidential',
        'restricted',
    ];

    protected $fillable = [
        'title',
        'record_type',
        'legal_category',
        'reference_number',
        'issuing_authority',
        'jurisdiction',
        'legal_basis',
        'issue_date',
        'expiration_date',
        'due_date',
        'next_action_date',
        'next_action',
        'status',
        'priority',
        'confidentiality_level',
        'responsible_officer_email',
        'assigned_user_id',
        'document_id',
        'file_uri',
        'file_name',
        'review_status',
        'legal_notes',
        'description',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiration_date' => 'date',
            'due_date' => 'date',
            'next_action_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(
            ArchiveDocument::class,
            'document_id'
        );
    }

    public function archiveDocuments(): HasMany
    {
        return $this->hasMany(
            ArchiveDocument::class,
            'linked_legal_record_id'
        );
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_user_id'
        );
    }

    public function deadlineDate(): ?CarbonInterface
    {
        $dates = collect([
            $this->due_date,
            $this->expiration_date,
            $this->next_action_date,
        ])->filter();

        if ($dates->isEmpty()) {
            return null;
        }

        return $dates
            ->sortBy(
                fn (CarbonInterface $date) => $date->timestamp
            )
            ->first();
    }

    public function deadlineState(): string
    {
        if ($this->status === 'closed') {
            return 'closed';
        }

        $today = now()->startOfDay();

        if (
            $this->expiration_date &&
            $this->expiration_date
                ->copy()
                ->startOfDay()
                ->lt($today)
        ) {
            return 'expired';
        }

        if (
            $this->due_date &&
            $this->due_date
                ->copy()
                ->startOfDay()
                ->lt($today)
        ) {
            return 'overdue';
        }

        if (
            $this->due_date &&
            $this->due_date->isToday()
        ) {
            return 'due_today';
        }

        if (
            $this->due_date &&
            $this->due_date
                ->copy()
                ->startOfDay()
                ->lte($today->copy()->addDays(7))
        ) {
            return 'due_soon';
        }

        if (
            $this->expiration_date &&
            $this->expiration_date->isToday()
        ) {
            return 'expires_today';
        }

        if (
            $this->expiration_date &&
            $this->expiration_date
                ->copy()
                ->startOfDay()
                ->lte($today->copy()->addDays(30))
        ) {
            return 'expiring_soon';
        }

        if (
            $this->next_action_date &&
            $this->next_action_date
                ->copy()
                ->startOfDay()
                ->lt($today)
        ) {
            return 'action_overdue';
        }

        return 'current';
    }

    public function deadlineLabel(): string
    {
        return match ($this->deadlineState()) {
            'closed' => 'Closed',
            'expired' => 'Expired',
            'overdue' => 'Overdue',
            'due_today' => 'Due Today',
            'due_soon' => 'Due Soon',
            'expires_today' => 'Expires Today',
            'expiring_soon' => 'Expiring Soon',
            'action_overdue' => 'Action Overdue',
            default => 'Current',
        };
    }
}
