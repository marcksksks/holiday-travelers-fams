<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CredentialRevocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class CredentialRevocationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create([
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }

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
                'Credential Revocation Test',
            'payload' =>
                'credential-revocation-test',
            'last_activity' => time(),
        ]);
    }

    public function test_revoke_all_removes_all_credentials(): void
    {
        $user = $this->user();

        $user->forceFill([
            'remember_token' =>
                str_repeat('r', 60),
        ])->save();

        $this->createSession(
            $user,
            'revoke-all-session-one'
        );

        $this->createSession(
            $user,
            'revoke-all-session-two'
        );

        $user->createToken(
            'revoke-all-token-one'
        );

        $user->createToken(
            'revoke-all-token-two'
        );

        app(
            CredentialRevocationService::class
        )->revokeAll($user);

        $user->refresh();

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

    public function test_revoke_other_sessions_preserves_current_session(): void
    {
        $user = $this->user();

        $user->forceFill([
            'remember_token' =>
                str_repeat('r', 60),
        ])->save();

        $this->createSession(
            $user,
            'current-session'
        );

        $this->createSession(
            $user,
            'other-session-one'
        );

        $this->createSession(
            $user,
            'other-session-two'
        );

        $user->createToken(
            'password-change-token'
        );

        app(
            CredentialRevocationService::class
        )->revokeOtherSessions(
            $user,
            'current-session'
        );

        $user->refresh();

        $this->assertNull(
            $user->remember_token
        );

        $this->assertDatabaseHas(
            config(
                'session.table',
                'sessions'
            ),
            [
                'id' => 'current-session',
                'user_id' => $user->id,
            ]
        );

        $this->assertDatabaseMissing(
            config(
                'session.table',
                'sessions'
            ),
            [
                'id' => 'other-session-one',
            ]
        );

        $this->assertDatabaseMissing(
            config(
                'session.table',
                'sessions'
            ),
            [
                'id' => 'other-session-two',
            ]
        );

        $this->assertSame(
            0,
            $user->tokens()->count()
        );
    }

    public function test_revocation_does_not_affect_another_user(): void
    {
        $target = $this->user();
        $other = $this->user();

        $this->createSession(
            $target,
            'target-session'
        );

        $this->createSession(
            $other,
            'other-user-session'
        );

        $target->createToken(
            'target-token'
        );

        $other->createToken(
            'other-user-token'
        );

        app(
            CredentialRevocationService::class
        )->revokeAll($target);

        $this->assertDatabaseHas(
            config(
                'session.table',
                'sessions'
            ),
            [
                'id' => 'other-user-session',
                'user_id' => $other->id,
            ]
        );

        $this->assertSame(
            1,
            $other->tokens()->count()
        );
    }

    public function test_revoke_other_sessions_requires_current_session_id(): void
    {
        $user = $this->user();

        $this->expectException(
            InvalidArgumentException::class
        );

        app(
            CredentialRevocationService::class
        )->revokeOtherSessions(
            $user,
            '   '
        );
    }
}