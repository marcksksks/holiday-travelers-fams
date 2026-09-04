<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'department',
        'owner_email',
        'confidentiality',
        'document_date',
        'expiration_date',
        'status',
        'version',
        'file_uri',
        'file_name',
        'uploaded_by_email',
        'linked_contract_id',
        'linked_legal_record_id',
        'linked_reservation_id',
        'linked_visitor_id',
        'container_id',
        'source_module',
        'system_key',
        'is_system_generated',
        'history',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'expiration_date' => 'date',
            'version' => 'integer',
            'history' => 'array',
            'is_system_generated' => 'boolean',
        ];
    }

    public function linkedContract()
    {
        return $this->belongsTo(Contract::class, 'linked_contract_id');
    }

    public function linkedLegalRecord()
    {
        return $this->belongsTo(LegalRecord::class, 'linked_legal_record_id');
    }

    public function linkedReservation()
    {
        return $this->belongsTo(Reservation::class, 'linked_reservation_id');
    }

    public function linkedVisitor()
    {
        return $this->belongsTo(Visitor::class, 'linked_visitor_id');
    }

    public function relatedModuleLabel(): ?string
    {
        return match (true) {
            $this->linkedContract !== null => 'Contract',
            $this->linkedLegalRecord !== null => 'Legal Record',
            $this->linkedReservation !== null => 'Facility Reservation',
            $this->linkedVisitor !== null => 'Visitor Record',
            default => null,
        };
    }

    public function relatedRecordLabel(): ?string
    {
        if ($this->linkedContract) {
            $number = $this->linkedContract->contract_number;

            return $number
                ? $number.' - '.$this->linkedContract->title
                : $this->linkedContract->title;
        }

        if ($this->linkedLegalRecord) {
            $reference = $this->linkedLegalRecord->reference_number;

            return $reference
                ? $reference.' - '.$this->linkedLegalRecord->title
                : $this->linkedLegalRecord->title;
        }

        if ($this->linkedReservation) {
            $date = $this->linkedReservation->date?->format('M d, Y');

            return trim(
                $this->linkedReservation->facility_name.
                ($date ? ' - '.$date : '')
            );
        }

        if ($this->linkedVisitor) {
            $date = $this->linkedVisitor->check_in_at?->format('M d, Y')
                ?? $this->linkedVisitor->created_at?->format('M d, Y');

            return trim(
                $this->linkedVisitor->full_name.
                ($date ? ' - '.$date : '')
            );
        }

        return null;
    }

    public function container()
    {
        return $this->belongsTo(
            DocumentContainer::class,
            'container_id'
        );
    }

    public function retentionRecord()
    {
        return $this->hasOne(
            RecordRetention::class,
            'record_id'
        )->where(
            'record_type',
            'document'
        );
    }
}