<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'facility_type' => $this->facility_type,
            'status' => $this->status,
            'equipment' => $this->equipment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
