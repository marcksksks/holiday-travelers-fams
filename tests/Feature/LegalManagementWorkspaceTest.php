<?php

namespace Tests\Feature;

use App\Models\LegalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalManagementWorkspaceTest extends TestCase
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

    public function test_admin_sees_full_legal_management_workspace(): void
    {
        $admin =
            $this->user(
                User::ROLE_ADMIN_OFFICER
            );

        $response =
            $this
                ->actingAs($admin)
                ->get(
                    route('legal.index')
                );

        $response
            ->assertOk()
            ->assertSee('Legal Management')
            ->assertSee('Legal Record Register')
            ->assertSee('New Legal Record')
            ->assertSee('Total Matters')
            ->assertSee('Action Required')
            ->assertSee('Due Soon')
            ->assertSee('Overdue / Expired')
            ->assertSee('All Priorities')
            ->assertSee('All Officers')
            ->assertSee('All Deadlines');
    }

    public function test_manager_can_view_workspace_without_create_controls(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('legal.index')
            )
            ->assertOk()
            ->assertSee(
                'Legal Management'
            )
            ->assertDontSee(
                'New Legal Record'
            );
    }

    public function test_workspace_displays_derived_expired_state_separately_from_active_status(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        LegalRecord::create([
            'title' => 'Expired Active Permit',

            'record_type' => 'permit',

            'status' => 'active',

            'priority' => 'high',

            'confidentiality_level' => 'internal',

            'expiration_date' => now()
                ->subDays(3)
                ->toDateString(),
        ]);

        $this
            ->actingAs($manager)
            ->get(
                route('legal.index')
            )
            ->assertOk()
            ->assertSee(
                'Expired Active Permit'
            )
            ->assertSee(
                'Active'
            )
            ->assertSee(
                'Expired'
            );
    }

    public function test_legal_officer_sees_review_action(): void
    {
        $officer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        LegalRecord::create([
            'title' => 'Review Matter',

            'record_type' => 'legal_case',

            'status' => 'active',

            'priority' => 'critical',

            'confidentiality_level' => 'restricted',
        ]);

        $this
            ->actingAs($officer)
            ->get(
                route('legal.index')
            )
            ->assertOk()
            ->assertSee(
                'Legal Review'
            );
    }

    public function test_edit_page_contains_management_workspace_fields(): void
    {
        $admin =
            $this->user(
                User::ROLE_ADMIN_OFFICER
            );

        $record =
            LegalRecord::create([
                'title' => 'Editable Legal Matter',

                'record_type' => 'requirement',

                'status' => 'active',

                'priority' => 'high',

                'confidentiality_level' => 'confidential',
            ]);

        $this
            ->actingAs($admin)
            ->get(
                route(
                    'legal.edit',
                    $record
                )
            )
            ->assertOk()
            ->assertSee(
                'Priority'
            )
            ->assertSee(
                'Confidentiality'
            )
            ->assertSee(
                'Assigned Officer'
            )
            ->assertSee(
                'Next Required Action'
            )
            ->assertSee(
                'Legal Basis / Regulation'
            );
    }
}
