<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function employee(): User
    {
        return User::factory()
            ->role(User::ROLE_EMPLOYEE)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_settings_is_available_from_primary_sidebar(): void
    {
        $user =
            $this->employee();

        $this
            ->actingAs($user)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'data-sidebar-tooltip="Settings"',
                false
            )
            ->assertSee(
                'aria-label="Settings"',
                false
            );
    }

    public function test_settings_navigation_is_available_to_every_application_role(): void
    {
        foreach (
            array_keys(User::ROLES) as $role
        ) {
            $this->assertContains(
                'settings.index',
                Rbac::navFor($role)
            );
        }
    }

    public function test_settings_workspace_uses_shared_application_hierarchy(): void
    {
        $user =
            $this->employee();

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route('settings.index')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Personal Workspace'
            )
            ->assertSee(
                'Settings Navigation'
            )
            ->assertSee(
                'Personal Profile'
            )
            ->assertSee(
                'Security'
            )
            ->assertSee(
                'Appearance'
            )
            ->assertSee(
                'Application Information'
            )
            ->assertSee(
                'id="settings-profile"',
                false
            )
            ->assertSee(
                'id="settings-security"',
                false
            )
            ->assertSee(
                'id="settings-appearance"',
                false
            )
            ->assertSee(
                'id="settings-system"',
                false
            );
    }

    public function test_settings_view_uses_shared_page_and_section_components(): void
    {
        $source =
            file_get_contents(
                resource_path(
                    'views/settings/index.blade.php'
                )
            );

        $this->assertStringContainsString(
            '<x-page-header',
            $source
        );

        $this->assertStringContainsString(
            '<x-section-header',
            $source
        );

        $this->assertStringContainsString(
            'xl:grid-cols-2',
            $source
        );

        $this->assertStringContainsString(
            'data-settings-primary-column',
            $source
        );

        $this->assertStringContainsString(
            'data-settings-security-column',
            $source
        );

        $this->assertStringContainsString(
            'items-start',
            $source
        );
    }

    public function test_settings_link_is_not_duplicated_in_profile_dropdown(): void
    {
        $layout =
            file_get_contents(
                resource_path(
                    'views/layouts/app.blade.php'
                )
            );

        $this->assertStringNotContainsString(
            "@if (Route::has('settings.index'))",
            $layout
        );

        $this->assertStringContainsString(
            "'settings.index' => [",
            $layout
        );

        $this->assertStringContainsString(
            "'label' => 'Account'",
            $layout
        );
    }

    public function test_profile_update_accepts_current_users_existing_email(): void
    {
        $user =
            $this->employee();

        $response =
            $this
                ->actingAs($user)
                ->put(
                    route('settings.profile.update'),
                    [
                        'full_name' => 'Updated Employee',
                        'email' => $user->email,
                    ]
                );

        $response
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas(
                'status',
                'Profile settings updated successfully.'
            );

        $this->assertDatabaseHas(
            'users',
            [
                'id' => $user->id,
                'full_name' => 'Updated Employee',
                'email' => $user->email,
            ]
        );
    }

    public function test_profile_update_rejects_another_users_email(): void
    {
        $existing =
            $this->employee();

        $user =
            $this->employee();

        $originalEmail =
            $user->email;

        $response =
            $this
                ->actingAs($user)
                ->from(
                    route('settings.index')
                )
                ->put(
                    route('settings.profile.update'),
                    [
                        'full_name' => $user->full_name,
                        'email' => $existing->email,
                    ]
                );

        $response
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'email'
            );

        $this->assertSame(
            $originalEmail,
            $user
                ->fresh()
                ->email
        );
    }
}
