<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationsWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function user(
        string $role
    ): User {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_privileged_reservation_workspace_uses_shared_design_system(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        Facility::create([
            'name' => 'Reservation Workspace Room',

            'facility_type' => 'meeting_room',

            'status' => 'available',

            'capacity' => 20,
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route('reservations.index')
            )
            ->assertOk()
            ->assertSee(
                'Approval Workspace'
            )
            ->assertSee(
                'Reservation Overview'
            )
            ->assertSee(
                'Total Requests'
            )
            ->assertSee(
                'Pending'
            )
            ->assertSee(
                'Approved'
            )
            ->assertSee(
                'Completed'
            )
            ->assertSee(
                'Reserve Facility'
            )
            ->assertSee(
                'Facility Schedule'
            )
            ->assertSee(
                'Reservation Requests'
            );
    }

    public function test_employee_sees_personal_reservation_workspace(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($employee)
            ->get(
                route('reservations.index')
            )
            ->assertOk()
            ->assertSee(
                'Personal Requests'
            )
            ->assertSee(
                'Reservation Overview'
            )
            ->assertSee(
                'My Reservations'
            )
            ->assertDontSee(
                'Approval Workspace'
            );
    }

    public function test_reservation_workspace_keeps_existing_filters_and_schedule(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('reservations.index')
            )
            ->assertOk()
            ->assertSee(
                'Search reservations...'
            )
            ->assertSee(
                'All statuses'
            )
            ->assertSee(
                'All facilities'
            )
            ->assertSee(
                'Open Calendar'
            );
    }
}
