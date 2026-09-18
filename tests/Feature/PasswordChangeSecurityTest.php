<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeSecurityTest extends TestCase
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
                'Password Change Security Test',
            'payload' =>
                'password-change-test',
            'last_activity' => time(),
        ]);
    }

    public function test_password_change_revokes_other_credentials_but_keeps_current_user_authenticated(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'CurrentPassword123!'
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
            'other-password-session-one'
        );

        $this->createSession(
            $user,
            'other-password-session-two'
        );

        $user->createToken(
            'password-change-token-one'
        );

        $user->createToken(
            'password-change-token-two'
        );

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'password.change.update'
                ),
                [
                    'current_password' =>
                        'CurrentPassword123!',
                    'password' =>
                        'NewSecurePassword456!',
                    'password_confirmation' =>
                        'NewSecurePassword456!',
                ]
            );

        $response
            ->assertRedirect(
                route('dashboard')
            )
            ->assertSessionHas(
                'status',
                'Password updated.'
            );

        $this->assertAuthenticatedAs(
            $user
        );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'NewSecurePassword456!',
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
    }

    public function test_wrong_current_password_does_not_change_or_revoke_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'CurrentPassword123!'
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
            'preserved-password-session'
        );

        $user->createToken(
            'preserved-password-token'
        );

        $response = $this
            ->actingAs($user)
            ->from(
                route('password.change')
            )
            ->put(
                route(
                    'password.change.update'
                ),
                [
                    'current_password' =>
                        'WrongPassword123!',
                    'password' =>
                        'NewSecurePassword456!',
                    'password_confirmation' =>
                        'NewSecurePassword456!',
                ]
            );

        $response
            ->assertRedirect(
                route('password.change')
            )
            ->assertSessionHasErrors(
                'current_password'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'CurrentPassword123!',
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
                    'preserved-password-session',
                'user_id' =>
                    $user->id,
            ]
        );

        $this->assertSame(
            1,
            $user->tokens()->count()
        );

        $this->assertAuthenticatedAs(
            $user
        );
    }

    public function test_password_confirmation_is_required_before_credentials_are_changed(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'CurrentPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->createSession(
            $user,
            'confirmation-protected-session'
        );

        $user->createToken(
            'confirmation-protected-token'
        );

        $this
            ->actingAs($user)
            ->from(
                route('password.change')
            )
            ->put(
                route(
                    'password.change.update'
                ),
                [
                    'current_password' =>
                        'CurrentPassword123!',
                    'password' =>
                        'NewSecurePassword456!',
                    'password_confirmation' =>
                        'DifferentPassword456!',
                ]
            )
            ->assertRedirect(
                route('password.change')
            )
            ->assertSessionHasErrors(
                'password'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'CurrentPassword123!',
                $user->password
            )
        );

        $this->assertDatabaseHas(
            config(
                'session.table',
                'sessions'
            ),
            [
                'id' =>
                    'confirmation-protected-session',
            ]
        );

        $this->assertSame(
            1,
            $user->tokens()->count()
        );
    }
}