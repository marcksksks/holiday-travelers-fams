<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SixRoleMutationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function roles(): array
    {
        return [
            User::ROLE_EMPLOYEE,
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];
    }

    private function allowed(
        string $role,
        array $allowedRoles
    ): bool {
        return in_array(
            $role,
            $allowedRoles,
            true
        );
    }

    public function test_api_mutations_require_authentication(): void
    {
        $this->postJson('/api/facilities', [
            'name' => 'Unauthenticated Room',
            'facility_type' => 'meeting_room',
            'status' => 'available',
        ])->assertUnauthorized();
    }

    public function test_facility_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);
            $before = Facility::count();

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/facilities', [
                    'name' => "Facility {$role}",
                    'facility_type' => 'meeting_room',
                    'status' => 'available',
                    'capacity' => 20,
                ]);

            if ($this->allowed($role, $allowed)) {
                $response->assertCreated();

                $this->assertSame(
                    $before + 1,
                    Facility::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    Facility::count()
                );
            }
        }
    }

    public function test_appointment_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);
            $before = Appointment::count();

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/appointments', [
                    'visitor_name' =>
                        "Appointment Visitor {$role}",

                    'visitor_type' => 'guest',

                    'date' =>
                        now()
                            ->addDays(10)
                            ->toDateString(),

                    'start_time' => '09:00',
                    'end_time' => '10:00',

                    'purpose' =>
                        'RBAC mutation test',
                ]);

            if ($this->allowed($role, $allowed)) {
                $response->assertCreated();

                $this->assertSame(
                    $before + 1,
                    Appointment::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    Appointment::count()
                );
            }
        }
    }

    public function test_visitor_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);
            $before = Visitor::count();

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/visitors', [
                    'full_name' =>
                        "Visitor {$role}",

                    'visitor_type' => 'guest',
                    'is_walk_in' => true,

                    'purpose' =>
                        'RBAC mutation test',
                ]);

            if ($this->allowed($role, $allowed)) {
                $response->assertCreated();

                $this->assertSame(
                    $before + 1,
                    Visitor::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    Visitor::count()
                );
            }
        }
    }

    public function test_document_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);
            $before = ArchiveDocument::count();

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/documents', [
                    'title' =>
                        "Manual Document {$role}",

                    'category' =>
                        'administrative',

                    'confidentiality' =>
                        'general',

                    'status' =>
                        'active',

                    'description' =>
                        'Mutation security test document.',
                ]);

            if ($this->allowed($role, $allowed)) {
                $response->assertCreated();

                $this->assertSame(
                    $before + 1,
                    ArchiveDocument::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    ArchiveDocument::count()
                );
            }
        }
    }

    public function test_legal_record_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);
            $before = LegalRecord::count();

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/legal-records', [
                    'title' =>
                        "Legal Record {$role}",

                    'record_type' =>
                        'permit',

                    'status' =>
                        'active',

                    'description' =>
                        'RBAC mutation test.',
                ]);

            if ($this->allowed($role, $allowed)) {
                $response->assertCreated();

                $this->assertSame(
                    $before + 1,
                    LegalRecord::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    LegalRecord::count()
                );
            }
        }
    }

    public function test_contract_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);
            $before = Contract::count();

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/contracts', [
                    'title' =>
                        "Contract {$role}",

                    'contract_type' =>
                        'service',

                    'description' =>
                        'RBAC mutation test.',

                    'responsible_officer_email' =>
                        $actor->email,
                ]);

            if ($this->allowed($role, $allowed)) {
                $response->assertCreated();

                $this->assertSame(
                    $before + 1,
                    Contract::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    Contract::count()
                );
            }
        }
    }

    public function test_every_authenticated_role_can_submit_reservation(): void
    {
        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $facility = Facility::factory()
                ->create([
                    'status' => 'available',
                    'capacity' => 20,
                ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson('/api/reservations', [
                    'facility_id' =>
                        $facility->id,

                    'date' =>
                        now()
                            ->addDays(20)
                            ->toDateString(),

                    'start_time' => '09:00',
                    'end_time' => '10:00',

                    'attendees' => 2,

                    'purpose' =>
                        'Authenticated reservation test',
                ]);

            $response->assertCreated();

            $this->assertDatabaseHas(
                'reservations',
                [
                    'facility_id' =>
                        $facility->id,

                    'requester_email' =>
                        $actor->email,

                    'status' =>
                        'pending',
                ]
            );
        }
    }

    public function test_reservation_decision_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $owner = User::factory()
                ->role(User::ROLE_EMPLOYEE)
                ->create();

            $facility = Facility::factory()
                ->create([
                    'status' => 'available',
                    'capacity' => 20,
                ]);

            $reservation = Reservation::create([
                'facility_id' =>
                    $facility->id,

                'facility_name' =>
                    $facility->name,

                'requester_email' =>
                    $owner->email,

                'requester_name' =>
                    $owner->full_name,

                'date' =>
                    now()
                        ->addDays(30)
                        ->toDateString(),

                'start_time' =>
                    '13:00',

                'end_time' =>
                    '14:00',

                'attendees' =>
                    2,

                'purpose' =>
                    'Decision RBAC test',

                'status' =>
                    'pending',
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson(
                    "/api/reservations/{$reservation->id}/decide",
                    [
                        'decision' =>
                            'approved',

                        'note' =>
                            'RBAC test',
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();

                $this->assertSame(
                    'approved',
                    $reservation
                        ->refresh()
                        ->status
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'pending',
                    $reservation
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_visitor_check_in_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $visitor = Visitor::create([
                'full_name' =>
                    "Check In Visitor {$role}",

                'visitor_type' =>
                    'guest',

                'is_walk_in' =>
                    true,

                'status' =>
                    'expected',
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson(
                    "/api/visitors/{$visitor->id}/check-in",
                    [
                        'badge_number' =>
                            "TEST-{$visitor->id}",
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();

                $this->assertSame(
                    'checked_in',
                    $visitor
                        ->refresh()
                        ->status
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'expected',
                    $visitor
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_contract_submit_for_review_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        $this->user(
            User::ROLE_LEGAL_OFFICER
        );

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $responsible = User::factory()
                ->create();

            $contract = Contract::create([
                'title' =>
                    "Submit Review {$role}",

                'contract_type' =>
                    'service',

                'responsible_officer_email' =>
                    $responsible->email,

                'status' =>
                    'draft',

                'legal_review_status' =>
                    'pending',

                'approval_status' =>
                    'not_submitted',
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson(
                    "/api/contracts/{$contract->id}/submit-review"
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();

                $this->assertSame(
                    'under_review',
                    $contract
                        ->refresh()
                        ->status
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'draft',
                    $contract
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_contract_legal_review_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        $this->user(
            User::ROLE_MANAGER
        );

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $responsible = User::factory()
                ->create();

            $contract = Contract::create([
                'title' =>
                    "Legal Review {$role}",

                'contract_type' =>
                    'service',

                'responsible_officer_email' =>
                    $responsible->email,

                'status' =>
                    'under_review',

                'legal_review_status' =>
                    'pending',

                'approval_status' =>
                    'not_submitted',
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson(
                    "/api/contracts/{$contract->id}/legal-review",
                    [
                        'outcome' =>
                            'approved',

                        'comments' =>
                            'Legal review passed.',
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();

                $contract->refresh();

                $this->assertSame(
                    'pending_approval',
                    $contract->status
                );

                $this->assertSame(
                    'approved',
                    $contract->legal_review_status
                );
            } else {
                $response->assertForbidden();

                $contract->refresh();

                $this->assertSame(
                    'under_review',
                    $contract->status
                );

                $this->assertSame(
                    'pending',
                    $contract->legal_review_status
                );
            }
        }
    }

    public function test_contract_final_decision_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $responsible = User::factory()
                ->create();

            $contract = Contract::create([
                'title' =>
                    "Final Decision {$role}",

                'contract_type' =>
                    'service',

                'responsible_officer_email' =>
                    $responsible->email,

                'status' =>
                    'pending_approval',

                'legal_review_status' =>
                    'approved',

                'approval_status' =>
                    'pending',
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson(
                    "/api/contracts/{$contract->id}/decide",
                    [
                        'decision' =>
                            'approve',

                        'comments' =>
                            'Management approval test.',
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();

                $contract->refresh();

                $this->assertSame(
                    'active',
                    $contract->status
                );

                $this->assertSame(
                    'approved',
                    $contract->approval_status
                );
            } else {
                $response->assertForbidden();

                $contract->refresh();

                $this->assertSame(
                    'pending_approval',
                    $contract->status
                );

                $this->assertSame(
                    'pending',
                    $contract->approval_status
                );
            }
        }
    }

    public function test_legal_edit_page_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $legal = LegalRecord::create([
                'title' => "Editable Legal {$role}",
                'record_type' => 'permit',
                'status' => 'active',
            ]);

            $response = $this
                ->actingAs($actor)
                ->get(
                    "/legal/{$legal->id}/edit"
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();
                $response->assertSee('Edit Legal Record');
            } else {
                $response->assertForbidden();
            }
        }
    }

    public function test_contract_edit_page_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $contract = Contract::create([
                'title' => "Editable Contract {$role}",
                'contract_type' => 'service',
                'status' => 'draft',
                'legal_review_status' => 'not_submitted',
                'approval_status' => 'not_submitted',
            ]);

            $response = $this
                ->actingAs($actor)
                ->get(
                    "/contracts/{$contract->id}/edit"
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();
                $response->assertSee('Edit Contract');
            } else {
                $response->assertForbidden();
            }
        }
    }

    public function test_contract_renewal_matches_manage_contracts_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $contract = Contract::create([
                'title' => "Renewal Contract {$role}",
                'contract_type' => 'service',
                'status' => 'active',
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'legal_review_status' => 'approved',
                'approval_status' => 'approved',
            ]);

            $newEndDate = now()
                ->addMonths(6)
                ->toDateString();

            $response = $this
                ->actingAs($actor)
                ->post(
                    "/contracts/{$contract->id}/renew",
                    [
                        'new_end_date' => $newEndDate,
                        'comments' => 'Renewal RBAC regression test.',
                    ]
                );

            $contract->refresh();

            if ($this->allowed($role, $allowed)) {
                $response->assertStatus(302);

                $this->assertSame(
                    'renewed',
                    $contract->status
                );

                $this->assertSame(
                    'pending',
                    $contract->legal_review_status
                );

                $this->assertSame(
                    'not_submitted',
                    $contract->approval_status
                );

                $this->assertSame(
                    $newEndDate,
                    $contract->end_date->toDateString()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'active',
                    $contract->status
                );

                $this->assertSame(
                    'approved',
                    $contract->legal_review_status
                );

                $this->assertSame(
                    'approved',
                    $contract->approval_status
                );
            }
        }
    }
}