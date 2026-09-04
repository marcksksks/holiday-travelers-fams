<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentContainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'path',
        'module',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(
            DocumentContainer::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            DocumentContainer::class,
            'parent_id'
        )->orderBy('name');
    }

    public function documents()
    {
        return $this->hasMany(
            ArchiveDocument::class,
            'container_id'
        );
    }
}