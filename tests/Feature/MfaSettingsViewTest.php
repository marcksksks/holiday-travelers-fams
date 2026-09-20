<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MfaSettingsViewTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD =
        'MfaViewPassword123!';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' =>
                'array',

            'two-factor.safe_devices.enabled' =>
                false,
        ]);

        Cache::flush();
    }

    private function user(): User
    {
        return User::factory()->create([
            'email' =>
                'mfa-view@example.test',

            'password' =>
                Hash::make(
                    self::PASSWORD
                ),

            'is_active' =>
                true,

            'force_password_change' =>
                false,
        ]);
    }

    private function enabledUser(): User
    {
        $user =
            $this->user();

        $user->createTwoFactorAuth();

        $code =
            $user->makeTwoFactorCode();

        $this->assertTrue(
            $user->confirmTwoFactorAuth(
                $code
            )
        );

        Cache::flush();

        return $user->fresh();
    }

    public function test_disabled_user_sees_enable_mfa_interface(): void
    {
        $user =
            $this->user();

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route('settings.index')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Enable Two-Factor Authentication'
            )
            ->assertSee(
                'MFA Disabled'
            );

        $response->assertViewHas(
            'mfaEnabled',
            false
        );

        $response->assertViewHas(
            'mfaQrCode',
            null
        );

        $response->assertViewHas(
            'mfaSecret',
            null
        );
    }

    public function test_authorized_pending_setup_exposes_qr_and_secret(): void
    {
        $user =
            $this->user();

        $twoFactor =
            $user->createTwoFactorAuth();

        $secret =
            $twoFactor->toString();

        $response =
            $this
                ->actingAs($user)
                ->withSession([
                    'mfa_setup_authorized_at' =>
                        now()->timestamp,
                ])
                ->get(
                    route('settings.index')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Scan the QR Code'
            )
            ->assertSee(
                'Confirm and Enable MFA'
            )
            ->assertSee(
                $secret
            );

        $response->assertViewHas(
            'mfaSetupAuthorized',
            true
        );

        $response->assertViewHas(
            'mfaQrCode',
            static fn ($qr): bool =>
                is_string($qr)
                && str_contains(
                    $qr,
                    '<svg'
                )
        );

        $response->assertViewHas(
            'mfaSecret',
            $secret
        );
    }

    public function test_expired_pending_setup_does_not_expose_qr_or_secret(): void
    {
        $user =
            $this->user();

        $twoFactor =
            $user->createTwoFactorAuth();

        $secret =
            $twoFactor->toString();

        $response =
            $this
                ->actingAs($user)
                ->withSession([
                    'mfa_setup_authorized_at' =>
                        now()
                            ->subMinutes(11)
                            ->timestamp,
                ])
                ->get(
                    route('settings.index')
                );

        $response
            ->assertOk()
            ->assertSee(
                'MFA Setup Authorization Expired'
            )
            ->assertSee(
                'Restart MFA Setup'
            )
            ->assertDontSee(
                $secret
            );

        $response->assertViewHas(
            'mfaSetupAuthorized',
            false
        );

        $response->assertViewHas(
            'mfaQrCode',
            null
        );

        $response->assertViewHas(
            'mfaSecret',
            null
        );
    }

    public function test_enabled_user_sees_mfa_management_without_shared_secret(): void
    {
        $user =
            $this->enabledUser();

        $secret =
            $user
                ->twoFactorAuth()
                ->firstOrFail()
                ->shared_secret;

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route('settings.index')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Two-Factor Authentication Enabled'
            )
            ->assertSee(
                'Generate New Recovery Codes'
            )
            ->assertSee(
                'Disable Two-Factor Authentication'
            )
            ->assertDontSee(
                $secret
            );

        $response->assertViewHas(
            'mfaEnabled',
            true
        );

        $response->assertViewHas(
            'mfaQrCode',
            null
        );

        $response->assertViewHas(
            'mfaSecret',
            null
        );
    }
}
