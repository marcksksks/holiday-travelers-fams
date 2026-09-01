<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'contact_number', 'email', 'organization', 'visitor_type',
        'purpose', 'host_email', 'host_name', 'appointment_id', 'id_reference',
        'badge_number', 'is_walk_in', 'status', 'check_in_at', 'check_out_at',
        'duration_minutes', 'ai_summary', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_walk_in' => 'boolean',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
