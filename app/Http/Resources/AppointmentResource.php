<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visitor_name' => $this->visitor_name,
            'visitor_organization' => $this->visitor_organization,
            'visitor_email' => $this->visitor_email,
            'visitor_contact' => $this->visitor_contact,
            'visitor_type' => $this->visitor_type,
            'host_email' => $this->host_email,
            'host_name' => $this->host_name,
            'date' => $this->date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'purpose' => $this->purpose,
            'facility_id' => $this->facility_id,
            'facility_name' => $this->facility_name,
            'status' => $this->status,
        ];
    }
}
