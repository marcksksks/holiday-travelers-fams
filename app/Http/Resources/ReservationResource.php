<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'facility_id' => $this->facility_id,
            'facility_name' => $this->facility_name,
            'requester_email' => $this->requester_email,
            'requester_name' => $this->requester_name,
            'date' => $this->date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'attendees' => $this->attendees,
            'purpose' => $this->purpose,
            'status' => $this->status,
            'decision_by_email' => $this->decision_by_email,
            'decision_at' => $this->decision_at,
            'decision_note' => $this->decision_note,
        ];
    }
}
