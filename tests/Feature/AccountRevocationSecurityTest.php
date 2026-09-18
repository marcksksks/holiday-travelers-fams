<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountRevocationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_deactivation_revokes_all_existing_credentials(): void
    {
        $admin = User::factory()
            ->role(User::ROLE_SYS_ADMIN)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);

        $target = User::factory()
            ->role(User::ROLE_EMPLOYEE)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);

        $target->forceFill([
            'remember_token' => str_repeat('r', 60),
        ])->save();

        $sessionTable = config(
            'session.table',
            'sessions'
        );

        DB::table($sessionTable)->insert([
            [
                'id' => 'revocation-session-one',
                'user_id' => $target->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Security Test',
                'payload' => 'security-test-payload',
                'last_activity' => time(),
            ],
            [
                'id' => 'revocation-session-two',
                'user_id' => $target->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Security Test',
                'payload' => 'security-test-payload',
                'last_activity' => time(),
            ],
        ]);

        $target->createToken(
            'security-test-token-one'
        );

        $target->createToken(
            'security-test-token-two'
        );

        $this->assertSame(
            2,
            DB::table($sessionTable)
                ->where('user_id', $target->id)
                ->count()
        );

        $this->assertSame(
            2,
            $target->tokens()->count()
        );

        app(UserManagementService::class)
            ->setActive(
                $admin,
                $target,
                false
            );

        $target->refresh();

        $this->assertFalse(
            $target->is_active
        );

        $this->assertNull(
            $target->remember_token
        );

        $this->assertSame(
            0,
            DB::table($sessionTable)
                ->where('user_id', $target->id)
                ->count()
        );

        $this->assertSame(
            0,
            $target->tokens()->count()
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $admin->email,
                'actor_role' => $admin->app_role,
                'action' => 'update',
                'module' => 'users',
                'record_label' =>
                    "User - {$target->full_name}",
                'record_id' =>
                    (string) $target->id,
                'details' => 'Deactivated',
            ]
        );
    }

    public function test_deactivating_one_user_does_not_revoke_another_users_credentials(): void
    {
        $admin = User::factory()
            ->role(User::ROLE_SYS_ADMIN)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);

        $target = User::factory()
            ->role(User::ROLE_EMPLOYEE)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);

        $other = User::factory()
            ->role(User::ROLE_EMPLOYEE)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);

        $sessionTable = config(
            'session.table',
            'sessions'
        );

        DB::table($sessionTable)->insert([
            'id' => 'other-user-session',
            'user_id' => $other->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Security Test',
            'payload' => 'security-test-payload',
            'last_activity' => time(),
        ]);

        $other->createToken(
            'other-user-token'
        );

        app(UserManagementService::class)
            ->setActive(
                $admin,
                $target,
                false
            );

        $this->assertDatabaseHas(
            $sessionTable,
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

    public function test_reactivation_does_not_create_new_credentials(): void
    {
        $admin = User::factory()
            ->role(User::ROLE_SYS_ADMIN)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);

        $target = User::factory()
            ->role(User::ROLE_EMPLOYEE)
            ->create([
                'is_active' => false,
                'force_password_change' => false,
            ]);

        app(UserManagementService::class)
            ->setActive(
                $admin,
                $target,
                true
            );

        $target->refresh();

        $this->assertTrue(
            $target->is_active
        );

        $this->assertSame(
            0,
            $target->tokens()->count()
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $admin->email,
                'action' => 'update',
                'module' => 'users',
                'record_id' =>
                    (string) $target->id,
                'details' => 'Reactivated',
            ]
        );
    }
}