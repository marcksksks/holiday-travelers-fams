<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'location', 'capacity', 'facility_type',
        'status', 'equipment', 'updated_by_email',
    ];

    protected function casts(): array
    {
        return ['equipment' => 'array', 'capacity' => 'integer'];
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
