<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number', 'title', 'contract_type', 'parties', 'start_date', 'end_date',
        'value', 'currency', 'description', 'responsible_officer_email', 'status',
        'legal_review_status', 'legal_review_notes', 'legal_reviewed_by', 'legal_reviewed_at',
        'approval_status', 'approved_by', 'approved_at', 'version', 'renewals',
        'document_id', 'file_uri', 'file_name',
    ];

    protected function casts(): array
    {
        return [
            'parties' => 'array',
            'renewals' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'value' => 'decimal:2',
            'legal_reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'version' => 'integer',
        ];
    }

    public function document()
    {
        return $this->belongsTo(ArchiveDocument::class, 'document_id');
    }

    public function archiveDocuments()
    {
        return $this->hasMany(ArchiveDocument::class, 'linked_contract_id');
    }
}
