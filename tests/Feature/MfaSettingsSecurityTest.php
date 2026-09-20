<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MfaSettingsSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD =
        'SettingsMfaPassword123!';

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
                'settings-mfa@example.test',

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

    private function enabledMfaUser(): User
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

    public function test_guest_cannot_manage_mfa(): void
    {
        $this->post(
            route('settings.mfa.setup'),
            [
                'current_password' =>
                    self::PASSWORD,
            ]
        )
            ->assertRedirect(
                route('login')
            );
    }

    public function test_current_password_is_required_to_start_mfa_setup(): void
    {
        $user =
            $this->user();

        $response =
            $this
                ->actingAs($user)
                ->from(
                    route('settings.index')
                )
                ->post(
                    route(
                        'settings.mfa.setup'
                    ),
                    [
                        'current_password' =>
                            'WrongPassword123!',
                    ]
                );

        $response
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'current_password'
            );

        $this->assertFalse(
            $user
                ->fresh()
                ->twoFactorAuth()
                ->exists()
        );
    }

    public function test_starting_mfa_setup_creates_pending_secret_but_does_not_enable_mfa(): void
    {
        $user =
            $this->user();

        $response =
            $this
                ->actingAs($user)
                ->post(
                    route(
                        'settings.mfa.setup'
                    ),
                    [
                        'current_password' =>
                            self::PASSWORD,
                    ]
                );

        $response->assertRedirect(
            route('settings.index')
        );

        $user->refresh();

        $this->assertTrue(
            $user
                ->twoFactorAuth()
                ->exists()
        );

        $this->assertFalse(
            $user->hasTwoFactorEnabled()
        );

        $response->assertSessionHas(
            'mfa_setup_authorized_at'
        );
    }

    public function test_enabled_mfa_cannot_be_rotated_by_setup_endpoint(): void
    {
        $user =
            $this->enabledMfaUser();

        $originalSecret =
            $user
                ->twoFactorAuth()
                ->firstOrFail()
                ->shared_secret;

        $response =
            $this
                ->actingAs($user)
                ->from(
                    route('settings.index')
                )
                ->post(
                    route(
                        'settings.mfa.setup'
                    ),
                    [
                        'current_password' =>
                            self::PASSWORD,
                    ]
                );

        $response
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'mfa'
            );

        $user->refresh();

        $this->assertTrue(
            $user->hasTwoFactorEnabled()
        );

        $this->assertSame(
            $originalSecret,
            $user
                ->twoFactorAuth()
                ->firstOrFail()
                ->shared_secret
        );
    }

    public function test_invalid_confirmation_code_does_not_enable_mfa(): void
    {
        $user =
            $this->user();

        $this
            ->actingAs($user)
            ->post(
                route(
                    'settings.mfa.setup'
                ),
                [
                    'current_password' =>
                        self::PASSWORD,
                ]
            )
            ->assertRedirect(
                route('settings.index')
            );

        $response =
            $this
                ->from(
                    route('settings.index')
                )
                ->post(
                    route(
                        'settings.mfa.confirm'
                    ),
                    [
                        '2fa_code' =>
                            '000000',
                    ]
                );

        $response
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                '2fa_code'
            );

        $this->assertFalse(
            $user
                ->fresh()
                ->hasTwoFactorEnabled()
        );
    }

    public function test_valid_confirmation_enables_mfa_and_flashes_encrypted_recovery_codes(): void
    {
        $user =
            $this->user();

        $this
            ->actingAs($user)
            ->post(
                route(
                    'settings.mfa.setup'
                ),
                [
                    'current_password' =>
                        self::PASSWORD,
                ]
            )
            ->assertRedirect(
                route('settings.index')
            );

        $code =
            $user
                ->fresh()
                ->makeTwoFactorCode();

        $response =
            $this->post(
                route(
                    'settings.mfa.confirm'
                ),
                [
                    '2fa_code' =>
                        $code,
                ]
            );

        $response->assertRedirect(
            route('settings.index')
        );

        $user->refresh();

        $this->assertTrue(
            $user->hasTwoFactorEnabled()
        );

        $response->assertSessionMissing(
            'mfa_setup_authorized_at'
        );

        $response->assertSessionHas(
            'mfa_recovery_codes'
        );

        $encrypted =
            session(
                'mfa_recovery_codes'
            );

        $this->assertIsString(
            $encrypted
        );

        $this->assertStringNotContainsString(
            $user
                ->getRecoveryCodes()
                ->first()['code'],
            $encrypted
        );

        $codes =
            json_decode(
                Crypt::decryptString(
                    $encrypted
                ),
                true
            );

        $this->assertCount(
            10,
            $codes
        );

        $this->assertSame(
            $user
                ->getRecoveryCodes()
                ->pluck('code')
                ->values()
                ->all(),
            $codes
        );
    }

    public function test_confirm_requires_recent_password_authorized_setup_session(): void
    {
        $user =
            $this->user();

        $user->createTwoFactorAuth();

        $code =
            $user->makeTwoFactorCode();

        $response =
            $this
                ->actingAs($user)
                ->from(
                    route('settings.index')
                )
                ->post(
                    route(
                        'settings.mfa.confirm'
                    ),
                    [
                        '2fa_code' =>
                            $code,
                    ]
                );

        $response
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                '2fa_code'
            );

        $this->assertFalse(
            $user
                ->fresh()
                ->hasTwoFactorEnabled()
        );
    }

    public function test_recovery_codes_require_current_password_and_replace_previous_codes(): void
    {
        $user =
            $this->enabledMfaUser();

        $original =
            $user
                ->getRecoveryCodes()
                ->pluck('code')
                ->values()
                ->all();

        $this
            ->actingAs($user)
            ->from(
                route('settings.index')
            )
            ->post(
                route(
                    'settings.mfa.recovery.regenerate'
                ),
                [
                    'current_password' =>
                        'WrongPassword123!',
                ]
            )
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'current_password'
            );

        $this->assertSame(
            $original,
            $user
                ->fresh()
                ->getRecoveryCodes()
                ->pluck('code')
                ->values()
                ->all()
        );

        $response =
            $this
                ->actingAs(
                    $user->fresh()
                )
                ->post(
                    route(
                        'settings.mfa.recovery.regenerate'
                    ),
                    [
                        'current_password' =>
                            self::PASSWORD,
                    ]
                );

        $response->assertRedirect(
            route('settings.index')
        );

        $freshCodes =
            $user
                ->fresh()
                ->getRecoveryCodes()
                ->pluck('code')
                ->values()
                ->all();

        $this->assertNotSame(
            $original,
            $freshCodes
        );

        $this->assertCount(
            10,
            $freshCodes
        );

        $response->assertSessionHas(
            'mfa_recovery_codes'
        );
    }

    public function test_disabling_mfa_requires_current_password(): void
    {
        $user =
            $this->enabledMfaUser();

        $this
            ->actingAs($user)
            ->from(
                route('settings.index')
            )
            ->delete(
                route(
                    'settings.mfa.disable'
                ),
                [
                    'current_password' =>
                        'WrongPassword123!',
                ]
            )
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'current_password'
            );

        $this->assertTrue(
            $user
                ->fresh()
                ->hasTwoFactorEnabled()
        );
    }

    public function test_valid_password_can_disable_mfa(): void
    {
        $user =
            $this->enabledMfaUser();

        $response =
            $this
                ->actingAs($user)
                ->delete(
                    route(
                        'settings.mfa.disable'
                    ),
                    [
                        'current_password' =>
                            self::PASSWORD,
                    ]
                );

        $response->assertRedirect(
            route('settings.index')
        );

        $user->refresh();

        $this->assertFalse(
            $user
                ->twoFactorAuth()
                ->exists()
        );

        $this->assertFalse(
            $user->hasTwoFactorEnabled()
        );
    }
}
