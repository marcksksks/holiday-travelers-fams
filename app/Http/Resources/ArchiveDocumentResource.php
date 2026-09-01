<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'department' => $this->department,
            'confidentiality' => $this->confidentiality,
            'status' => $this->status,
            'version' => $this->version,
            'document_date' => $this->document_date?->toDateString(),
            'expiration_date' => $this->expiration_date?->toDateString(),
            'file_name' => $this->file_name,
        ];
    }
}
