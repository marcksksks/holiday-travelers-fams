<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'record_type' => $this->record_type,
            'reference_number' => $this->reference_number,
            'issuing_authority' => $this->issuing_authority,
            'issue_date' => $this->issue_date?->toDateString(),
            'expiration_date' => $this->expiration_date?->toDateString(),
            'status' => $this->status,
            'review_status' => $this->review_status,
        ];
    }
}
