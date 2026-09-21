<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalRecordResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'title' => $this->title,

            'record_type' => $this->record_type,

            'legal_category' => $this->legal_category,

            'reference_number' => $this->reference_number,

            'issuing_authority' => $this->issuing_authority,

            'jurisdiction' => $this->jurisdiction,

            'legal_basis' => $this->legal_basis,

            'issue_date' => $this->issue_date
                ?->toDateString(),

            'expiration_date' => $this->expiration_date
                ?->toDateString(),

            'due_date' => $this->due_date
                ?->toDateString(),

            'next_action_date' => $this->next_action_date
                ?->toDateString(),

            'next_action' => $this->next_action,

            'status' => $this->status,

            'priority' => $this->priority,

            'confidentiality_level' => $this->confidentiality_level,

            'review_status' => $this->review_status,

            'deadline_state' => $this->deadlineState(),

            'deadline_label' => $this->deadlineLabel(),

            'responsible_officer_email' => $this->responsible_officer_email,

            'assigned_user_id' => $this->assigned_user_id,

            'assigned_officer' => $this->whenLoaded(
                'assignedOfficer',
                fn () => $this->assignedOfficer
                        ? [
                            'id' => $this
                                ->assignedOfficer
                                ->id,

                            'full_name' => $this
                                ->assignedOfficer
                                ->full_name,

                            'email' => $this
                                ->assignedOfficer
                                ->email,

                            'app_role' => $this
                                ->assignedOfficer
                                ->app_role,
                        ]
                        : null
            ),
        ];
    }
}
