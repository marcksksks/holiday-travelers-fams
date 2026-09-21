<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Facility;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\ContractWorkflowService;
use App\Services\ReservationService;
use App\Services\UserManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FamsSecurityAndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_deactivated_user_cannot_use_api(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $this->actingAs($user, 'sanctum')->getJson('/api/me')
            ->assertForbidden()
            ->assertJsonPath('code', 'account_deactivated');
    }

    public function test_forced_password_change_user_cannot_use_api(): void
    {
        $user = User::factory()->create(['force_password_change' => true]);
        $this->actingAs($user, 'sanctum')->getJson('/api/me')
            ->assertForbidden()
            ->assertJsonPath('code', 'password_change_required');
    }

    public function test_employee_cannot_view_documents_api(): void
    {
        $user = User::factory()->create(['app_role' => User::ROLE_EMPLOYEE]);
        $this->actingAs($user, 'sanctum')->getJson('/api/documents')->assertForbidden();
    }

    public function test_employee_can_only_view_their_reservations(): void
    {
        $facility = Facility::factory()->create(['status' => 'available']);
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $reservation = app(ReservationService::class)->submit($owner, [
            'facility_id' => $facility->id, 'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00', 'end_time' => '10:00',
        ]);

        $this->actingAs($other, 'sanctum')->getJson("/api/reservations/{$reservation->id}")->assertForbidden();
        $this->actingAs($owner, 'sanctum')->getJson("/api/reservations/{$reservation->id}")->assertOk();
    }

    public function test_reservation_capacity_is_enforced(): void
    {
        $facility = Facility::factory()->create(['capacity' => 10, 'status' => 'available']);
        $user = User::factory()->create();
        $this->expectException(ValidationException::class);
        app(ReservationService::class)->submit($user, [
            'facility_id' => $facility->id, 'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00', 'end_time' => '10:00', 'attendees' => 11,
        ]);
    }

    public function test_appointment_creation_always_starts_scheduled(): void
    {
        $facility = Facility::factory()->create(['status' => 'available']);
        $user = User::factory()->role(User::ROLE_MANAGER)->create();
        $appointment = app(AppointmentService::class)->create($user, [
            'visitor_name' => 'Test Visitor', 'visitor_type' => 'guest',
            'date' => now()->addDay()->toDateString(), 'start_time' => '09:00',
            'end_time' => '10:00', 'facility_id' => $facility->id, 'status' => 'completed',
        ]);
        $this->assertSame('scheduled', $appointment->status);
    }

    public function test_facility_appointment_requires_end_time(): void
    {
        $facility = Facility::factory()->create([
            'status' => 'available',
        ]);

        $manager = User::factory()
            ->role(User::ROLE_MANAGER)
            ->create();

        $this->expectException(
            ValidationException::class
        );

        app(AppointmentService::class)
            ->create($manager, [
                'visitor_name' => 'Facility Visitor',
                'visitor_type' => 'guest',
                'date' => now()->addDay()->toDateString(),
                'start_time' => '09:00',
                'facility_id' => $facility->id,
            ]);
    }

    public function test_last_active_system_admin_cannot_be_deactivated(): void
    {
        $admin = User::factory()->role(User::ROLE_SYS_ADMIN)->create();
        $this->expectException(ValidationException::class);
        app(UserManagementService::class)->setActive($admin, $admin, false);
    }

    public function test_contract_cannot_skip_legal_review(): void
    {
        $admin = User::factory()->role(User::ROLE_ADMIN_OFFICER)->create();
        $contract = Contract::create(['title' => 'Test', 'contract_type' => 'service', 'status' => 'draft']);
        $this->expectException(ValidationException::class);
        app(ContractWorkflowService::class)->decide(User::factory()->role(User::ROLE_MANAGER)->create(), $contract, true);
    }
}
