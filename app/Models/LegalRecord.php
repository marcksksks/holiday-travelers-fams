<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'record_type', 'reference_number', 'issuing_authority', 'issue_date',
        'expiration_date', 'status', 'responsible_officer_email', 'document_id',
        'file_uri', 'file_name', 'review_status', 'legal_notes', 'description',
    ];

    protected function casts(): array
    {
        return ['issue_date' => 'date', 'expiration_date' => 'date'];
    }

    public function document()
    {
        return $this->belongsTo(ArchiveDocument::class, 'document_id');
    }

    public function archiveDocuments()
    {
        return $this->hasMany(ArchiveDocument::class, 'linked_legal_record_id');
    }
}
