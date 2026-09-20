<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PrivilegedMfaEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD =
        'PrivilegedMfaPassword123!';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'two-factor.safe_devices.enabled' => false,
        ]);

        Cache::flush();
    }

    private function directUser(
        string $role,
        bool $enableMfa = false
    ): User {
        $user = User::query()->create([
            'full_name' => 'Privileged MFA Test',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make(self::PASSWORD),
            'app_role' => $role,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        if ($enableMfa) {
            $user->createTwoFactorAuth();

            $this->assertTrue(
                $user->confirmTwoFactorAuth(
                    $user->makeTwoFactorCode()
                )
            );

            Cache::flush();
        }

        return $user->fresh();
    }

    public function test_privileged_roles_without_mfa_are_blocked_from_application_pages(): void
    {
        foreach (
            User::MFA_REQUIRED_ROLES as $role
        ) {
            $user =
                $this->directUser($role);

            $this
                ->actingAs($user)
                ->get('/dashboard')
                ->assertRedirect(
                    route('settings.index')
                )
                ->assertSessionHasErrors(
                    'mfa'
                );
        }
    }

    public function test_employee_and_receptionist_do_not_require_mfa(): void
    {
        foreach ([
            User::ROLE_EMPLOYEE,
            User::ROLE_RECEPTIONIST,
        ] as $role) {
            $user =
                $this->directUser($role);

            $this
                ->actingAs($user)
                ->get('/dashboard')
                ->assertOk();
        }
    }

    public function test_blocked_privileged_user_can_access_mfa_setup(): void
    {
        $user =
            $this->directUser(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($user)
            ->get(
                route('settings.index')
            )
            ->assertOk();

        $this
            ->post(
                route('settings.mfa.setup'),
                [
                    'current_password' => self::PASSWORD,
                ]
            )
            ->assertRedirect(
                route('settings.index')
            );

        $this->assertTrue(
            $user
                ->fresh()
                ->twoFactorAuth()
                ->exists()
        );

        $this->assertFalse(
            $user
                ->fresh()
                ->hasTwoFactorEnabled()
        );
    }

    public function test_privileged_user_with_mfa_can_access_application(): void
    {
        $user =
            $this->directUser(
                User::ROLE_SYS_ADMIN,
                true
            );

        $this
            ->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_privileged_api_user_without_mfa_is_rejected(): void
    {
        $user =
            $this->directUser(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs(
                $user,
                'sanctum'
            )
            ->getJson('/api/me')
            ->assertForbidden()
            ->assertJson([
                'code' => 'mfa_required',
            ]);
    }

    public function test_required_mfa_cannot_be_disabled(): void
    {
        $user =
            $this->directUser(
                User::ROLE_SYS_ADMIN,
                true
            );

        $response =
            $this
                ->actingAs($user)
                ->from(
                    route('settings.index')
                )
                ->delete(
                    route('settings.mfa.disable'),
                    [
                        'current_password' => self::PASSWORD,
                    ]
                );

        $response
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'mfa'
            );

        $this->assertTrue(
            $user
                ->fresh()
                ->hasTwoFactorEnabled()
        );
    }
}
