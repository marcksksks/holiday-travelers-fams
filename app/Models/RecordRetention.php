<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordRetention extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_title',
        'record_type',
        'record_id',
        'policy_id',
        'policy_name',
        'start_date',
        'review_date',
        'status',
        'compliance_status',
        'last_action_by',
        'last_action_at',
        'notes',

        'disposition_status',
        'disposition_requested_by',
        'disposition_requested_at',
        'disposition_reason',
        'disposition_decided_by',
        'disposition_decided_at',
        'disposition_decision_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'review_date' => 'date',
            'last_action_at' => 'datetime',

            'disposition_requested_at' => 'datetime',
            'disposition_decided_at' => 'datetime',
        ];
    }

    public function policy()
    {
        return $this->belongsTo(
            RetentionPolicy::class,
            'policy_id'
        );
    }

    public function document()
    {
        return $this->belongsTo(
            ArchiveDocument::class,
            'record_id'
        );
    }
}