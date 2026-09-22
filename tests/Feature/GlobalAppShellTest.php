<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalAppShellTest extends TestCase
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

    public function test_authenticated_workspace_uses_accessible_application_shell(): void
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
            ->assertSee(
                'Skip to main content'
            )
            ->assertSee(
                'id="application-sidebar"',
                false
            )
            ->assertSee(
                'aria-label="Primary navigation"',
                false
            )
            ->assertSee(
                'aria-controls="application-sidebar"',
                false
            )
            ->assertSee(
                'id="main-content"',
                false
            )
            ->assertSee(
                'data-sidebar-nav-link',
                false
            );
    }

    public function test_application_shell_navigation_remains_role_aware(): void
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
            ->assertSee(
                'Facilities Reservation'
            )
            ->assertSee(
                'Appointments'
            )
            ->assertDontSee(
                'Staff Accounts'
            )
            ->assertDontSee(
                'Audit Trail'
            );
    }

    public function test_compact_navigation_links_have_accessible_tooltip_labels(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'data-sidebar-tooltip="Dashboard"',
                false
            )
            ->assertSee(
                'aria-label="Dashboard"',
                false
            )
            ->assertSee(
                'data-sidebar-tooltip="Facilities Reservation"',
                false
            )
            ->assertSee(
                'data-sidebar-tooltip="Staff Accounts"',
                false
            )
            ->assertSee(
                'aria-label="Holiday Travelers Dashboard"',
                false
            );
    }

    public function test_shell_source_preserves_compact_sidebar_mobile_navigation_and_popover_controls(): void
    {
        $javascript =
            file_get_contents(
                resource_path(
                    'js/app.js'
                )
            );

        $stylesheet =
            file_get_contents(
                resource_path(
                    'css/app.css'
                )
            );

        $this->assertStringContainsString(
            'fams-sidebar-collapsed',
            $javascript
        );

        $this->assertStringContainsString(
            'fams-mobile-nav-open',
            $javascript
        );

        $this->assertStringContainsString(
            'fams:close-profile-menu',
            $javascript
        );

        $this->assertStringContainsString(
            'fams:close-notification-menu',
            $javascript
        );

        $this->assertStringContainsString(
            'fams-sidebar-tooltip',
            $javascript
        );

        $this->assertStringContainsString(
            '[data-sidebar][data-collapsed="true"]',
            $stylesheet
        );

        $this->assertStringContainsString(
            '.fams-skip-link',
            $stylesheet
        );

        $this->assertStringContainsString(
            '.fams-sidebar-tooltip',
            $stylesheet
        );

        $this->assertStringContainsString(
            '[data-sidebar-nav-link][aria-current="page"]',
            $stylesheet
        );
    }
}
