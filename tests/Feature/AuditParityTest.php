<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ArchiveDocument;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use App\Services\AppointmentService;
use App\Services\VisitorCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuditParityTest extends TestCase
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

    private function assertAudit(
        string $email,
        string $action,
        string $module,
        int $recordId,
        ?string $details = null
    ): void {
        $expected = [
            'actor_email' => $email,
            'action' => $action,
            'module' => $module,
            'record_id' => (string) $recordId,
        ];

        if ($details !== null) {
            $expected['details'] = $details;
        }

        $this->assertDatabaseHas(
            'audit_logs',
            $expected
        );
    }

    public function test_facility_api_create_update_and_archive_are_audited(): void
    {
        $admin = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/facilities', [
                'name' => 'Audit Facility',
                'facility_type' => 'meeting_room',
                'status' => 'available',
                'capacity' => 20,
            ])
            ->assertCreated();

        $facility = Facility::where(
            'name',
            'Audit Facility'
        )->firstOrFail();

        $this->assertAudit(
            $admin->email,
            'create',
            'facilities',
            $facility->id
        );

        $this->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/facilities/{$facility->id}",
                [
                    'name' => 'Audit Facility Updated',
                    'facility_type' => 'meeting_room',
                    'status' => 'available',
                    'capacity' => 25,
                ]
            )
            ->assertOk();

        $this->assertAudit(
            $admin->email,
            'update',
            'facilities',
            $facility->id
        );

        $this->actingAs($admin, 'sanctum')
            ->deleteJson(
                "/api/facilities/{$facility->id}"
            )
            ->assertNoContent();

        $this->assertAudit(
            $admin->email,
            'archive',
            'facilities',
            $facility->id
        );

        $this->actingAs($admin, 'sanctum')
            ->patchJson(
                "/api/facilities/{$facility->id}/restore"
            )
            ->assertOk();

        $this->assertSame(
            'unavailable',
            $facility->refresh()->status
        );

        $this->assertAudit(
            $admin->email,
            'restore',
            'facilities',
            $facility->id
        );
    }

    public function test_appointment_cancel_and_visitor_registration_api_are_audited(): void
    {
        $receptionist = $this->user(
            User::ROLE_RECEPTIONIST
        );

        $appointment = Appointment::create([
            'visitor_name' => 'Audit Appointment',
            'visitor_type' => 'guest',
            'date' => now()->addDays(5)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'scheduled',
        ]);

        $this->actingAs(
            $receptionist,
            'sanctum'
        )
            ->deleteJson(
                "/api/appointments/{$appointment->id}"
            )
            ->assertNoContent();

        $this->assertAudit(
            $receptionist->email,
            'update',
            'appointments',
            $appointment->id,
            'Cancelled'
        );

        $this->actingAs(
            $receptionist,
            'sanctum'
        )
            ->postJson('/api/visitors', [
                'full_name' => 'Audit API Visitor',
                'visitor_type' => 'guest',
                'is_walk_in' => true,
                'privacy_acknowledged' => true,
            ])
            ->assertCreated();

        $visitor = Visitor::where(
            'full_name',
            'Audit API Visitor'
        )->firstOrFail();

        $this->assertAudit(
            $receptionist->email,
            'create',
            'visitors',
            $visitor->id,
            'Visitor registered'
        );
    }

    public function test_document_legal_and_contract_api_mutations_are_audited(): void
    {
        $admin = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/documents', [
                'title' => 'Audit API Document',
                'category' => 'administrative',
                'confidentiality' => 'general',
                'status' => 'active',
            ])
            ->assertCreated();

        $document = ArchiveDocument::where(
            'title',
            'Audit API Document'
        )->firstOrFail();

        $this->assertAudit(
            $admin->email,
            'upload',
            'documents',
            $document->id
        );

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/legal-records', [
                'title' => 'Audit API Legal',
                'record_type' => 'permit',
                'status' => 'active',
            ])
            ->assertCreated();

        $legal = LegalRecord::where(
            'title',
            'Audit API Legal'
        )->firstOrFail();

        $this->assertAudit(
            $admin->email,
            'create',
            'legal',
            $legal->id
        );

        $this->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/legal-records/{$legal->id}",
                [
                    'title' => 'Audit API Legal Updated',
                    'record_type' => 'permit',
                    'status' => 'active',
                ]
            )
            ->assertOk();

        $this->assertAudit(
            $admin->email,
            'update',
            'legal',
            $legal->id
        );

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/contracts', [
                'title' => 'Audit API Contract',
                'contract_type' => 'service',
            ])
            ->assertCreated();

        $contract = Contract::where(
            'title',
            'Audit API Contract'
        )->firstOrFail();

        $this->assertAudit(
            $admin->email,
            'create',
            'contracts',
            $contract->id
        );

        $this->actingAs($admin, 'sanctum')
            ->putJson(
                "/api/contracts/{$contract->id}",
                [
                    'title' => 'Audit API Contract Updated',
                    'contract_type' => 'service',
                ]
            )
            ->assertOk();

        $this->assertAudit(
            $admin->email,
            'update',
            'contracts',
            $contract->id
        );
    }

    public function test_reservation_api_cancellation_is_audited(): void
    {
        $employee = $this->user(
            User::ROLE_EMPLOYEE
        );

        $facility = Facility::factory()
            ->create([
                'status' => 'available',
            ]);

        $reservation = Reservation::create([
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'requester_email' => $employee->email,
            'requester_name' => $employee->full_name,
            'date' => now()->addDays(7)->toDateString(),
            'start_time' => '13:00',
            'end_time' => '14:00',
            'status' => 'pending',
        ]);

        $this->actingAs(
            $employee,
            'sanctum'
        )
            ->deleteJson(
                "/api/reservations/{$reservation->id}"
            )
            ->assertNoContent();

        $this->assertAudit(
            $employee->email,
            'update',
            'facilities',
            $reservation->id,
            'Cancelled'
        );
    }

    public function test_missing_web_mutations_are_now_audited(): void
    {
        $receptionist = $this->user(
            User::ROLE_RECEPTIONIST
        );

        $this->actingAs($receptionist)
            ->post('/visitors', [
                'full_name' => 'Audit Web Visitor',
                'visitor_type' => 'guest',
                'is_walk_in' => true,
                'privacy_acknowledged' => true,
            ])
            ->assertRedirect();

        $visitor = Visitor::where(
            'full_name',
            'Audit Web Visitor'
        )->firstOrFail();

        $this->assertAudit(
            $receptionist->email,
            'create',
            'visitors',
            $visitor->id,
            'Visitor registered'
        );

        $employee = $this->user(
            User::ROLE_EMPLOYEE
        );

        $facility = Facility::factory()
            ->create([
                'status' => 'available',
            ]);

        $reservation = Reservation::create([
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'requester_email' => $employee->email,
            'requester_name' => $employee->full_name,
            'date' => now()->addDays(8)->toDateString(),
            'start_time' => '14:00',
            'end_time' => '15:00',
            'status' => 'pending',
        ]);

        $this->actingAs($employee)
            ->delete(
                "/reservations/{$reservation->id}"
            )
            ->assertRedirect();

        $this->assertAudit(
            $employee->email,
            'update',
            'facilities',
            $reservation->id,
            'Cancelled'
        );
    }

    public function test_service_level_appointment_and_visitor_permissions_cannot_be_bypassed(): void
    {
        $employee = $this->user(
            User::ROLE_EMPLOYEE
        );

        $appointments = $this->app->make(
            AppointmentService::class
        );

        try {
            $appointments->create(
                $employee,
                [
                    'visitor_name' => 'Unauthorized Service Appointment',

                    'visitor_type' => 'guest',

                    'date' => now()
                        ->addDays(10)
                        ->toDateString(),

                    'start_time' => '09:00',

                    'end_time' => '10:00',
                ]
            );

            $this->fail(
                'AppointmentService allowed an unauthorized employee.'
            );
        } catch (ValidationException $exception) {
            $this->assertSame(
                403,
                $exception->status
            );
        }

        $visitor = Visitor::create([
            'full_name' => 'Unauthorized Service Visitor',

            'visitor_type' => 'guest',

            'status' => 'expected',
        ]);

        $checks = $this->app->make(
            VisitorCheckService::class
        );

        try {
            $checks->checkIn(
                $employee,
                $visitor
            );

            $this->fail(
                'VisitorCheckService allowed an unauthorized employee.'
            );
        } catch (ValidationException $exception) {
            $this->assertSame(
                403,
                $exception->status
            );
        }

        $this->assertSame(
            'expected',
            $visitor->refresh()->status
        );
    }
}
