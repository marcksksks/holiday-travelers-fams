<?php

namespace Tests\Feature;

use App\Models\LegalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWorkspaceTest extends TestCase
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

    public function test_dashboard_uses_polished_workspace_structure(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'Operations Dashboard'
            )
            ->assertSee(
                'Live'
            )
            ->assertSee(
                'System Overview'
            )
            ->assertSee(
                'Current Operations'
            )
            ->assertDontSee('Needs Attention')
            ->assertSee(
                'Upcoming Work'
            )
            ->assertDontSee('Quick Access');
    }

    public function test_employee_dashboard_remains_role_aware(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $response =
            $this
                ->actingAs($employee)
                ->get(
                    route('dashboard')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Available Facilities'
            )
            ->assertSee(
                'Pending Reservations'
            )
            ->assertDontSee(
                'Legal Action Required'
            )
            ->assertDontSee(
                'Contract Renewals'
            );
    }

    public function test_legal_officer_dashboard_surfaces_legal_workspace(): void
    {
        $legalOfficer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        $this
            ->actingAs($legalOfficer)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'Legal Action Required'
            )
            ->assertSee(
                'Legal Management'
            )
            ->assertDontSee(
                'Today&#039;s Appointments',
                false
            );
    }

    public function test_dashboard_uses_live_badge_instead_of_duplicate_role_badge(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'Live'
            );
    }

    public function test_dashboard_hides_attention_section_when_no_attention_items_exist(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($employee)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee('All Clear')->assertDontSee('All operational queues are clear')->assertDontSee('Needs Attention');
    }

    public function test_needs_attention_hides_zero_value_queues_when_other_work_exists(): void
    {
        $sysAdmin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        LegalRecord::create([
            'title' => 'Attention Test Matter',

            'record_type' => 'legal_case',

            'status' => 'active',

            'priority' => 'medium',

            'confidentiality_level' => 'internal',

            'review_status' => 'action_required',
        ]);

        $this
            ->actingAs($sysAdmin)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'Legal Follow-up'
            )
            ->assertDontSee(
                'Retention Reviews'
            )
            ->assertDontSee(
                'Contract Renewals'
            )
            ->assertDontSee(
                'Documents Needing Review'
            )
            ->assertDontSee(
                'Disposal Approvals'
            );
    }
}
