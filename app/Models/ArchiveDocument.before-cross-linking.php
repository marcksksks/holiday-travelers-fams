<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'category', 'department', 'owner_email',
        'confidentiality', 'document_date', 'expiration_date', 'status', 'version',
        'file_uri', 'file_name', 'uploaded_by_email', 'linked_contract_id', 'history',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'expiration_date' => 'date',
            'version' => 'integer',
            'history' => 'array',
        ];
    }

    public function linkedContract()
    {
        return $this->belongsTo(Contract::class, 'linked_contract_id');
    }
}
