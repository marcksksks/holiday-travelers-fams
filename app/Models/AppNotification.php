<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_email', 'title', 'body', 'module', 'severity', 'link', 'is_read',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
