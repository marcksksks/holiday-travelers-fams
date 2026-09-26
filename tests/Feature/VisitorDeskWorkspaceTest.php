<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorDeskWorkspaceTest extends TestCase
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

    public function test_receptionist_sees_front_desk_operations_workspace(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $this
            ->actingAs($receptionist)
            ->get(
                route('visitors.index')
            )
            ->assertOk()
            ->assertDontSee('Front Desk Operations')
            ->assertSee(
                'Visitor Status'
            )
            ->assertSee(
                'Expected'
            )
            ->assertSee(
                'Awaiting Host'
            )
            ->assertSee(
                'On Site'
            )
            ->assertSee(
                'Completed'
            )
            ->assertSee(
                'Visitor Traffic Intelligence'
            )
            ->assertDontSee(
                'Visitor Intelligence Assistant'
            )
            ->assertSee(
                'Register Visitor'
            )
            ->assertSee(
                'Visitor Activity'
            );
    }

    public function test_employee_sees_visitor_workspace_without_operational_actions(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($employee)
            ->get(
                route('visitors.index')
            )
            ->assertOk()
            ->assertSee(
                'Visitor Status'
            )
            ->assertSee(
                'Visitor Activity'
            )
            ->assertDontSee(
                'Register Visitor'
            )
            ->assertDontSee(
                'AI Assistant'
            );
    }

    public function test_visitor_activity_contains_responsive_mobile_cards(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        Visitor::create([
            'full_name' => 'Responsive Visitor',

            'visitor_type' => 'guest',

            'purpose' => 'Workspace verification',

            'is_walk_in' => true,

            'status' => 'expected',
        ]);

        $this
            ->actingAs($receptionist)
            ->get(
                route('visitors.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Visitor'
            )
            ->assertSee(
                'data-visitor-mobile-card',
                false
            )
            ->assertSee(
                'View Details'
            )
            ->assertSee(
                'Check In'
            );
    }

    public function test_existing_visitor_filters_remain_available(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $this
            ->actingAs($receptionist)
            ->get(
                route('visitors.index')
            )
            ->assertOk()
            ->assertSee('Search visitors...')
            ->assertSee('data-visitor-filter-bar', false)
            ->assertSee(
                'All statuses'
            )
            ->assertSee(
                'All visitor types'
            );
    }
}
