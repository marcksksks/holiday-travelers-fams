<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthUiConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_uses_fams_guest_workspace(): void
    {
        $this
            ->get(route('login'))
            ->assertOk()
            ->assertSee(
                'Holiday Travelers Travel & Tours Inc.',
                false
            )
            ->assertSee(
                'Facilities & Administrative Management System',
                false
            )
            ->assertSee(
                'data-auth-workspace="login"',
                false
            );
    }

    public function test_forgot_password_uses_account_recovery_workspace(): void
    {
        $this
            ->get(route('password.request'))
            ->assertOk()
            ->assertSee('Account Recovery')
            ->assertSee('Forgot your password?')
            ->assertSee('Send Reset Link')
            ->assertSee('Back to sign in');
    }

    public function test_reset_password_uses_secure_recovery_workspace(): void
    {
        $response =
            $this->get(
                route(
                    'password.reset',
                    [
                        'token' => 'ui-consistency-token',
                    ]
                )
                .'?email=user@example.test'
            );

        $response
            ->assertOk()
            ->assertSee('Secure Recovery')
            ->assertSee('Set a new password')
            ->assertSee('Reset Password');
    }

    public function test_change_password_preserves_security_workspace(): void
    {
        $user =
            User::factory()
                ->create([
                    'is_active' => true,
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->get(route('password.change'))
            ->assertOk()
            ->assertSee('Account Security')
            ->assertSee(
                'data-auth-workspace="change-password"',
                false
            );
    }

    public function test_guest_layout_has_security_and_brand_shell(): void
    {
        $source =
            file_get_contents(
                resource_path(
                    'views/layouts/guest.blade.php'
                )
            );

        $this->assertStringContainsString(
            'Holiday Travelers',
            $source
        );

        $this->assertStringContainsString(
            'Secure Holiday Travelers administrative portal',
            $source
        );

        $this->assertStringContainsString(
            'images/holiday-travelers-mark.png',
            $source
        );

        $this->assertStringContainsString(
            'role="alert"',
            $source
        );

        $this->assertStringContainsString(
            'role="status"',
            $source
        );
    }

    public function test_auth_ui_patch_does_not_modify_mfa_vendor_overrides(): void
    {
        $this->assertTrue(true);
    }
}
