<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ViewErrorBag;
use Laragear\TwoFactor\Facades\Auth2FA;
use Laragear\TwoFactor\TwoFactorLoginHelper;
use Tests\TestCase;

class MfaLoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'SecureMfaPassword123!';

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Keep TOTP replay-cache state isolated inside each test.
         */
        config([
            'cache.default' => 'array',
            'two-factor.safe_devices.enabled' => false,
        ]);

        Cache::flush();
    }

    private function postLogin(
        array $data
    ) {
        /*
         * Laravel Facades cache their resolved instances.
         *
         * Laragear's TwoFactorLoginHelper contains the Request instance
         * that existed when the facade was first resolved. A PHPUnit
         * feature test can issue multiple HTTP requests inside one
         * application lifecycle, unlike a normal browser request.
         *
         * Clearing only this facade instance makes each simulated request
         * resolve a helper containing the current Request.
         */
        Auth2FA::clearResolvedInstance(
            TwoFactorLoginHelper::class
        );

        return $this->post(
            '/login',
            $data
        );
    }

    private function createMfaUser(
        array $attributes = []
    ): User {
        $user = User::factory()->create(
            array_merge(
                [
                    'email' =>
                        'mfa-user@example.test',
                    'password' =>
                        Hash::make(self::PASSWORD),
                    'is_active' => true,
                    'force_password_change' => false,
                ],
                $attributes
            )
        );

        $user->createTwoFactorAuth();

        $confirmationCode =
            $user->makeTwoFactorCode();

        $this->assertTrue(
            $user->confirmTwoFactorAuth(
                $confirmationCode
            )
        );

        /*
         * Confirmation consumes the TOTP code. Clear only the test cache
         * so the authentication tests can generate a fresh validation.
         */
        Cache::flush();

        return $user->fresh();
    }

    public function test_mfa_user_is_challenged_after_valid_password(): void
    {
        $user = $this->createMfaUser();

        $response = $this->postLogin(
            [
                'email' => $user->email,
                'password' => self::PASSWORD,
            ]
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'two-factor::login'
            )
            ->assertViewHas(
                'input',
                '2fa_code'
            )
            ->assertSessionHas(
                config(
                    'two-factor.login.key',
                    '_2fa_login'
                )
            );

        $this->assertGuest();
    }

    public function test_valid_totp_completes_login(): void
    {
        $user = $this->createMfaUser();

        $this->postLogin(
            [
                'email' => $user->email,
                'password' => self::PASSWORD,
            ]
        )
            ->assertViewIs(
                'two-factor::login'
            );

        $code =
            $user->fresh()
                ->makeTwoFactorCode();

        $response = $this->postLogin(
            [
                '2fa_code' => $code,
            ]
        );

        $response->assertRedirect(
            route('dashboard')
        );

        $this->assertAuthenticatedAs(
            $user
        );

        $response->assertSessionMissing(
            config(
                'two-factor.login.key',
                '_2fa_login'
            )
        );
    }

    public function test_invalid_totp_does_not_authenticate(): void
    {
        $user = $this->createMfaUser();

        $this->postLogin(
            [
                'email' => $user->email,
                'password' => self::PASSWORD,
            ]
        )
            ->assertViewIs(
                'two-factor::login'
            );

        $response = $this->postLogin(
            [
                '2fa_code' =>
                    'INVALID-CODE-DOES-NOT-EXIST',
            ]
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'two-factor::login'
            )
            ->assertViewHas(
                'errors',
                static function (
                    ViewErrorBag $errors
                ): bool {
                    self::assertTrue(
                        $errors->has(
                            '2fa_code'
                        )
                    );

                    self::assertSame(
                        trans(
                            'two-factor::validation.totp_code',
                            [
                                'attribute' =>
                                    '2fa_code',
                            ]
                        ),
                        $errors->first(
                            '2fa_code'
                        )
                    );

                    return true;
                }
            )
            ->assertSessionHas(
                config(
                    'two-factor.login.key',
                    '_2fa_login'
                )
            );

        $this->assertGuest();
    }

    public function test_direct_two_factor_code_without_password_stage_is_rejected(): void
    {
        $user = $this->createMfaUser();

        $validCode =
            $user->makeTwoFactorCode();

        $response =
            $this
                ->from('/login')
                ->post(
                    '/login',
                    [
                        '2fa_code' =>
                            $validCode,
                    ]
                );

        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors(
                '2fa_code'
            );

        $this->assertGuest();

        $response->assertSessionMissing(
            config(
                'two-factor.login.key',
                '_2fa_login'
            )
        );
    }

    public function test_inactive_mfa_account_cannot_reach_mfa_challenge(): void
    {
        $user = $this->createMfaUser([
            'email' =>
                'inactive-mfa@example.test',
            'is_active' => false,
        ]);

        $response =
            $this
                ->from('/login')
                ->post(
                    '/login',
                    [
                        'email' =>
                            $user->email,
                        'password' =>
                            self::PASSWORD,
                    ]
                );

        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors(
                'email'
            )
            ->assertSessionMissing(
                config(
                    'two-factor.login.key',
                    '_2fa_login'
                )
            );

        $this->assertGuest();
    }

    public function test_recovery_code_authenticates_only_once(): void
    {
        $user = $this->createMfaUser([
            'email' =>
                'recovery-mfa@example.test',
        ]);

        $recoveryCode =
            $user
                ->getRecoveryCodes()
                ->first()['code'];

        $this->postLogin(
            [
                'email' =>
                    $user->email,
                'password' =>
                    self::PASSWORD,
            ]
        )
            ->assertViewIs(
                'two-factor::login'
            );

        $this->postLogin(
            [
                '2fa_code' =>
                    $recoveryCode,
            ]
        )
            ->assertRedirect(
                route('dashboard')
            );

        $this->assertAuthenticatedAs(
            $user
        );

        $this->post('/logout')
            ->assertRedirect(
                route('login')
            );

        $this->assertGuest();

        $this->postLogin(
            [
                'email' =>
                    $user->email,
                'password' =>
                    self::PASSWORD,
            ]
        )
            ->assertViewIs(
                'two-factor::login'
            );

        $response = $this->postLogin(
            [
                '2fa_code' =>
                    $recoveryCode,
            ]
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'two-factor::login'
            )
            ->assertViewHas(
                'errors',
                static function (
                    ViewErrorBag $errors
                ): bool {
                    self::assertTrue(
                        $errors->has(
                            '2fa_code'
                        )
                    );

                    return true;
                }
            )
            ->assertSessionHas(
                config(
                    'two-factor.login.key',
                    '_2fa_login'
                )
            );

        $this->assertGuest();
    }
}
