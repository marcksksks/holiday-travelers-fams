<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesWorkspaceTest extends TestCase
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

    public function test_facilities_workspace_uses_shared_design_system(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('facilities.index')
            )
            ->assertOk()
            ->assertSee(
                'Resource Directory'
            )
            ->assertSee(
                'Facility Status'
            )
            ->assertSee(
                'Total Facilities'
            )
            ->assertSee(
                'Available'
            )
            ->assertSee(
                'Maintenance'
            )
            ->assertSee(
                'Unavailable'
            )
            ->assertSee(
                'Add Facility'
            );
    }

    public function test_employee_can_view_workspace_without_management_action(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($employee)
            ->get(
                route('facilities.index')
            )
            ->assertOk()
            ->assertSee(
                'Facility Status'
            )
            ->assertDontSee(
                'Add Facility'
            );
    }

    public function test_facility_directory_contains_responsive_mobile_card(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        Facility::create([
            'name' => 'Responsive Test Room',

            'facility_type' => 'meeting_room',

            'status' => 'available',

            'capacity' => 12,

            'location' => 'Second Floor',
        ]);

        $this
            ->actingAs($employee)
            ->get(
                route('facilities.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Test Room'
            )
            ->assertSee(
                'data-facility-mobile-card',
                false
            )
            ->assertSee(
                'View Details'
            )
            ->assertSee(
                'Reserve'
            );
    }
}
