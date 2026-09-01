<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'facility_name', 'requester_email', 'requester_name',
        'date', 'start_time', 'end_time', 'attendees', 'purpose', 'status',
        'decision_by_email', 'decision_at', 'decision_note',
    ];

    protected function casts(): array
    {
        return ['date' => 'date', 'decision_at' => 'datetime', 'attendees' => 'integer'];
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
