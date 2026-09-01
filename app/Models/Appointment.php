<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_name', 'visitor_organization', 'visitor_email', 'visitor_contact',
        'visitor_type', 'host_email', 'host_name', 'date', 'start_time', 'end_time',
        'purpose', 'facility_id', 'facility_name', 'notes', 'status', 'visitor_id',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
