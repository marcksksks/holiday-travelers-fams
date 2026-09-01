<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'organization' => $this->organization,
            'visitor_type' => $this->visitor_type,
            'purpose' => $this->purpose,
            'host_email' => $this->host_email,
            'host_name' => $this->host_name,
            'appointment_id' => $this->appointment_id,
            'badge_number' => $this->badge_number,
            'is_walk_in' => $this->is_walk_in,
            'status' => $this->status,
            'check_in_at' => $this->check_in_at,
            'check_out_at' => $this->check_out_at,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
