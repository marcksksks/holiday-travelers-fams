<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailWorkspaceTest extends TestCase
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

    public function test_manager_sees_accountability_ledger_workspace(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('audit-trail.index')
            )
            ->assertOk()
            ->assertSee(
                'Read-Only Ledger'
            )
            ->assertSee(
                'Audit Overview'
            )
            ->assertSee(
                'Find Audit Events'
            )
            ->assertSee(
                'Audit Events'
            )
            ->assertSee(
                'Accountable Actors'
            )
            ->assertSee(
                'Audited Modules'
            );
    }

    public function test_audit_event_explorer_preserves_existing_filters(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('audit-trail.index')
            )
            ->assertOk()
            ->assertSee(
                'Email address'
            )
            ->assertSee(
                'All Modules'
            )
            ->assertSee(
                'All Actions'
            )
            ->assertSee(
                'Apply Filters'
            )
            ->assertSee(
                'Clear'
            );
    }

    public function test_audit_ledger_contains_responsive_mobile_card(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        AuditLog::create([
            'actor_email' => $admin->email,

            'actor_role' => User::ROLE_SYS_ADMIN,

            'action' => 'update',

            'module' => 'contracts',

            'record_label' => 'Contract · Responsive Audit Record',

            'record_id' => 88,

            'details' => 'Responsive audit ledger verification.',

            'created_at' => now(),
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route('audit-trail.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Audit Record'
            )
            ->assertSee(
                'data-audit-mobile-card',
                false
            )
            ->assertSee(
                'Responsive audit ledger verification.'
            );
    }

    public function test_audit_trail_remains_read_only_in_workspace(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route('audit-trail.index')
            )
            ->assertOk()
            ->assertSee(
                'historical accountability records'
            )
            ->assertDontSee(
                'Delete Event'
            )
            ->assertDontSee(
                'Edit Event'
            );
    }
}
