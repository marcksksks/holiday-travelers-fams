<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MfaChallengeViewTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD =
        'MfaChallengePassword123!';

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

    public function test_mfa_challenge_uses_fams_interface_without_bootstrap_cdn(): void
    {
        $user =
            User::factory()->create([
                'email' =>
                    'mfa-challenge@example.test',

                'password' =>
                    Hash::make(
                        self::PASSWORD
                    ),

                'is_active' =>
                    true,

                'force_password_change' =>
                    false,
            ]);

        $user->createTwoFactorAuth();

        $code =
            $user->makeTwoFactorCode();

        $this->assertTrue(
            $user->confirmTwoFactorAuth(
                $code
            )
        );

        Cache::flush();

        $response =
            $this->post(
                '/login',
                [
                    'email' =>
                        $user->email,

                    'password' =>
                        self::PASSWORD,
                ]
            );

        $response
            ->assertOk()
            ->assertViewIs(
                'two-factor::login'
            )
            ->assertSee(
                'Two-Factor Authentication'
            )
            ->assertSee(
                'Verification Required'
            )
            ->assertSee(
                'Authentication or Recovery Code'
            )
            ->assertSee(
                'Verify and Continue'
            )
            ->assertSee(
                'Back to Sign In'
            )
            ->assertDontSee(
                'cdn.jsdelivr.net/npm/bootstrap',
                false
            );

        $this->assertGuest();
    }
}
