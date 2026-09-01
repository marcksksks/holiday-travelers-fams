<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordRetention extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_title', 'record_type', 'record_id', 'policy_id', 'policy_name',
        'start_date', 'review_date', 'status', 'compliance_status',
        'last_action_by', 'last_action_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'review_date' => 'date', 'last_action_at' => 'datetime'];
    }

    public function policy()
    {
        return $this->belongsTo(RetentionPolicy::class, 'policy_id');
    }
}
