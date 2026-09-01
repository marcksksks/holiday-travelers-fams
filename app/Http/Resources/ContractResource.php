<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number,
            'title' => $this->title,
            'contract_type' => $this->contract_type,
            'parties' => $this->parties,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'value' => $this->value,
            'currency' => $this->currency,
            'status' => $this->status,
            'legal_review_status' => $this->legal_review_status,
            'approval_status' => $this->approval_status,
            'version' => $this->version,
        ];
    }
}
