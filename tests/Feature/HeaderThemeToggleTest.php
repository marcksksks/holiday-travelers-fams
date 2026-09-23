<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_header_exposes_light_dark_toggle(): void
    {
        $user =
            User::factory()
                ->role(
                    User::ROLE_EMPLOYEE
                )
                ->create([
                    'is_active' => true,
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'data-theme-toggle',
                false
            )
            ->assertSee(
                'data-theme-icon-dark',
                false
            )
            ->assertSee(
                'data-theme-icon-light',
                false
            );
    }

    public function test_profile_dropdown_does_not_duplicate_change_password_action(): void
    {
        $layout =
            file_get_contents(
                resource_path(
                    'views/layouts/app.blade.php'
                )
            );

        $this->assertStringNotContainsString(
            "@if (Route::has('password.change'))",
            $layout
        );

        $this->assertStringNotContainsString(
            "href=\"{{ route('password.change') }}\"",
            $layout
        );
    }

    public function test_header_toggle_uses_existing_persistent_theme_system(): void
    {
        $layout =
            file_get_contents(
                resource_path(
                    'views/layouts/app.blade.php'
                )
            );

        $this->assertStringContainsString(
            'window.FAMSTheme',
            $layout
        );

        $this->assertStringContainsString(
            'themeEffective',
            $layout
        );

        $this->assertStringContainsString(
            'fams-theme-change',
            $layout
        );
    }
}
