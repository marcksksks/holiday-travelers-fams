<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createSession(
        User $user,
        string $id
    ): void {
        DB::table(
            config(
                'session.table',
                'sessions'
            )
        )->insert([
            'id' => $id,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' =>
                'Password Reset Security Test',
            'payload' =>
                'password-reset-security-test',
            'last_activity' => time(),
        ]);
    }

    public function test_successful_password_reset_revokes_all_existing_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $user->forceFill([
            'remember_token' =>
                str_repeat('r', 60),
        ])->save();

        $this->createSession(
            $user,
            'reset-session-one'
        );

        $this->createSession(
            $user,
            'reset-session-two'
        );

        $user->createToken(
            'reset-token-one'
        );

        $user->createToken(
            'reset-token-two'
        );

        $resetToken =
            Password::broker()
                ->createToken($user);

        $response = $this->post(
            route('password.update'),
            [
                'token' => $resetToken,
                'email' => $user->email,
                'password' =>
                    'NewResetPassword456!',
                'password_confirmation' =>
                    'NewResetPassword456!',
            ]
        );

        $response
            ->assertRedirect(
                route('login')
            )
            ->assertSessionHas(
                'status'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'NewResetPassword456!',
                $user->password
            )
        );

        $this->assertFalse(
            $user->force_password_change
        );

        $this->assertNull(
            $user->remember_token
        );

        $this->assertSame(
            0,
            DB::table(
                config(
                    'session.table',
                    'sessions'
                )
            )
                ->where(
                    'user_id',
                    $user->id
                )
                ->count()
        );

        $this->assertSame(
            0,
            $user->tokens()->count()
        );

        $this->assertGuest();
    }

    public function test_successful_reset_token_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $resetToken =
            Password::broker()
                ->createToken($user);

        $this->post(
            route('password.update'),
            [
                'token' => $resetToken,
                'email' => $user->email,
                'password' =>
                    'FirstResetPassword456!',
                'password_confirmation' =>
                    'FirstResetPassword456!',
            ]
        )->assertRedirect(
            route('login')
        );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'FirstResetPassword456!',
                $user->password
            )
        );

        $this->from(
            route(
                'password.reset',
                $resetToken
            )
        )
            ->post(
                route('password.update'),
                [
                    'token' => $resetToken,
                    'email' => $user->email,
                    'password' =>
                        'SecondResetPassword789!',
                    'password_confirmation' =>
                        'SecondResetPassword789!',
                ]
            )
            ->assertSessionHasErrors(
                'email'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'FirstResetPassword456!',
                $user->password
            )
        );

        $this->assertFalse(
            Hash::check(
                'SecondResetPassword789!',
                $user->password
            )
        );
    }

    public function test_invalid_reset_token_does_not_change_or_revoke_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $rememberToken =
            str_repeat('r', 60);

        $user->forceFill([
            'remember_token' =>
                $rememberToken,
        ])->save();

        $this->createSession(
            $user,
            'invalid-reset-session'
        );

        $user->createToken(
            'invalid-reset-api-token'
        );

        $this->from('/forgot-password')
            ->post(
                route('password.update'),
                [
                    'token' =>
                        'invalid-reset-token',
                    'email' =>
                        $user->email,
                    'password' =>
                        'NewResetPassword456!',
                    'password_confirmation' =>
                        'NewResetPassword456!',
                ]
            )
            ->assertRedirect(
                '/forgot-password'
            )
            ->assertSessionHasErrors(
                'email'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'OldPassword123!',
                $user->password
            )
        );

        $this->assertSame(
            $rememberToken,
            $user->remember_token
        );

        $this->assertDatabaseHas(
            config(
                'session.table',
                'sessions'
            ),
            [
                'id' =>
                    'invalid-reset-session',
                'user_id' =>
                    $user->id,
            ]
        );

        $this->assertSame(
            1,
            $user->tokens()->count()
        );
    }

    public function test_password_confirmation_failure_does_not_consume_reset_token(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $resetToken =
            Password::broker()
                ->createToken($user);

        $this->from(
            route(
                'password.reset',
                $resetToken
            )
        )
            ->post(
                route('password.update'),
                [
                    'token' => $resetToken,
                    'email' => $user->email,
                    'password' =>
                        'NewResetPassword456!',
                    'password_confirmation' =>
                        'DifferentPassword456!',
                ]
            )
            ->assertSessionHasErrors(
                'password'
            );

        $this->post(
            route('password.update'),
            [
                'token' => $resetToken,
                'email' => $user->email,
                'password' =>
                    'NewResetPassword456!',
                'password_confirmation' =>
                    'NewResetPassword456!',
            ]
        )->assertRedirect(
            route('login')
        );

        $this->assertTrue(
            Hash::check(
                'NewResetPassword456!',
                $user
                    ->refresh()
                    ->password
            )
        );
    }
}