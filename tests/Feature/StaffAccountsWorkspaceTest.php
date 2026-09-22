<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAccountsWorkspaceTest extends TestCase
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

    public function test_system_admin_sees_access_governance_workspace(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('users.index')
            )
            ->assertOk()
            ->assertSee(
                'Access Governance'
            )
            ->assertSee(
                'Access Overview'
            )
            ->assertSee(
                'Total Staff'
            )
            ->assertSee(
                'Active Accounts'
            )
            ->assertSee(
                'Deactivated'
            )
            ->assertSee(
                'Password Change'
            )
            ->assertSee(
                'Staff Directory'
            )
            ->assertSee(
                'Add Staff'
            );
    }

    public function test_staff_directory_exposes_security_policy_without_changing_rbac(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        User::factory()
            ->role(
                User::ROLE_LEGAL_OFFICER
            )
            ->create([
                'full_name' => 'Privileged Security Test',

                'is_active' => true,

                'force_password_change' => false,
            ]);

        User::factory()
            ->role(
                User::ROLE_EMPLOYEE
            )
            ->create([
                'full_name' => 'Standard Security Test',

                'is_active' => true,

                'force_password_change' => false,
            ]);

        $this
            ->actingAs($admin)
            ->get(
                route('users.index')
            )
            ->assertOk()
            ->assertSee(
                'Privileged Security Test'
            )
            ->assertSee(
                'Standard Security Test'
            )
            ->assertSee(
                'MFA Required'
            )
            ->assertSee(
                'MFA Optional'
            );
    }

    public function test_staff_directory_contains_responsive_mobile_cards(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        User::factory()
            ->role(
                User::ROLE_EMPLOYEE
            )
            ->create([
                'full_name' => 'Responsive Staff Member',

                'is_active' => true,

                'force_password_change' => false,
            ]);

        $this
            ->actingAs($admin)
            ->get(
                route('users.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Staff Member'
            )
            ->assertSee(
                'data-staff-mobile-card',
                false
            )
            ->assertSee(
                'Manage Account'
            );
    }

    public function test_existing_staff_filters_remain_available(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('users.index')
            )
            ->assertOk()
            ->assertSee(
                'Search by name, email, department or job title...'
            )
            ->assertSee(
                'All Roles'
            )
            ->assertSee(
                'All Statuses'
            );
    }

    public function test_non_system_admin_remains_forbidden_from_staff_accounts(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('users.index')
            )
            ->assertForbidden();
    }
}
