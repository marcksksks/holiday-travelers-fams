<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetentionPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'record_category', 'retention_years', 'description', 'legal_basis', 'is_active',
    ];

    protected function casts(): array
    {
        return ['retention_years' => 'integer', 'is_active' => 'boolean'];
    }

    public function recordRetentions()
    {
        return $this->hasMany(RecordRetention::class, 'policy_id');
    }
}
