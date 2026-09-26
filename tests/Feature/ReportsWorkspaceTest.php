<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsWorkspaceTest extends TestCase
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

    public function test_manager_sees_management_intelligence_workspace(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('reports.index')
            )
            ->assertOk()
            ->assertDontSee('Operational Intelligence')
            ->assertSee(
                'Report Window'
            )
            ->assertSee('data-report-filter-bar', false)
            ->assertSee(
                'Executive Summary'
            )
            ->assertSee(
                'Compliance Watch'
            )
            ->assertSee(
                'Operational Breakdowns'
            )
            ->assertSee(
                'Facility Utilization'
            );
    }

    public function test_report_workspace_keeps_date_controls_and_quick_ranges(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('reports.index')
            )
            ->assertOk()
            ->assertSee(
                'From Date'
            )
            ->assertSee(
                'To Date'
            )
            ->assertSee(
                'Update Report'
            )
            ->assertSee(
                'Last 7 days'
            )
            ->assertSee(
                'Last 30 days'
            )
            ->assertSee(
                'This month'
            );
    }

    public function test_report_scope_explains_period_and_current_state_metrics(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('reports.index')
            )
            ->assertOk()
            ->assertSee(
                'Current lifecycle and deadline conditions'
            )
            ->assertSee(
                'Report scope:'
            );
    }
}
