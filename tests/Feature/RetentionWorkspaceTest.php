<?php

namespace Tests\Feature;

use App\Models\RecordRetention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetentionWorkspaceTest extends TestCase
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

    public function test_system_admin_sees_governance_workspace(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('retention.index')
            )
            ->assertOk()
            ->assertDontSee('Governance Workspace')
            ->assertSee(
                'Compliance Status'
            )
            ->assertSee(
                'Tracked Records'
            )
            ->assertSee(
                'Compliant'
            )
            ->assertSee(
                'At Risk'
            )
            ->assertSee(
                'Review Required'
            )
            ->assertSee(
                'Retention Register'
            )
            ->assertSee('Track Record');
    }

    public function test_legal_officer_sees_records_without_management_actions(): void
    {
        $legalOfficer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        $this
            ->actingAs($legalOfficer)
            ->get(
                route('retention.index')
            )
            ->assertOk()
            ->assertSee(
                'Compliance Status'
            )
            ->assertSee(
                'Retention Register'
            )
            ->assertDontSee(
                'Track Retention Record'
            )
            ->assertDontSee(
                'Retention Policies'
            );
    }

    public function test_retention_register_contains_responsive_mobile_card(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        RecordRetention::create([
            'record_title' => 'Responsive Retention Record',

            'record_type' => 'document',

            'record_id' => 999,

            'review_date' => now()
                ->addDays(20)
                ->toDateString(),

            'status' => 'retained',

            'compliance_status' => 'compliant',

            'last_action_by' => $admin->email,

            'last_action_at' => now(),
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route('retention.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Retention Record'
            )
            ->assertSee(
                'data-retention-mobile-card',
                false
            )
            ->assertSee(
                'Manage Record'
            );
    }

    public function test_existing_retention_tabs_and_filters_remain_available(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('retention.index')
            )
            ->assertOk()
            ->assertSee(
                'Records'
            )
            ->assertSee(
                'Disposal Queue'
            )
            ->assertSee(
                'Retention Policies'
            )
            ->assertSee('Search retention records...')
            ->assertSee('data-retention-filter-bar', false)
            ->assertSee(
                'All types'
            )
            ->assertSee(
                'Recently updated'
            );
    }
}
