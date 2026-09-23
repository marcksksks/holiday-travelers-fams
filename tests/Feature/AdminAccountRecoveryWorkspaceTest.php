<?php

namespace Tests\Feature;

use App\Models\AccountRecoveryRequest;
use App\Models\AppNotification;
use App\Models\User;
use App\Services\AccountRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccountRecoveryWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function user(
        string $role
    ): User {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function pendingRecovery(
        User $user
    ): AccountRecoveryRequest {
        $result =
            app(
                AccountRecoveryService::class
            )->requestAdminRecovery(
                $user->email,
                '127.0.0.1',
                'Admin Recovery Workspace Test'
            );

        $this->assertNotNull(
            $result
        );

        return $result['request'];
    }

    public function test_system_admin_can_view_recovery_workspace(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $target =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $recovery =
            $this->pendingRecovery(
                $target
            );

        $this
            ->actingAs(
                $administrator
            )
            ->get(
                route(
                    'account-recovery.admin.index'
                )
            )
            ->assertOk()
            ->assertSee(
                'Account Recovery'
            )
            ->assertSee(
                'Recovery Activity'
            )
            ->assertSee(
                'Recovery Requests'
            )
            ->assertSee(
                $target->full_name
            )
            ->assertSee(
                $recovery->reference
            )
            ->assertSee(
                'Verify &amp; Authorize',
                false
            )
            ->assertSee(
                'data-recovery-admin-workspace',
                false
            )
            ->assertSee(
                'data-recovery-mobile-card',
                false
            );
    }

    public function test_non_system_admin_cannot_view_recovery_workspace(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs(
                $manager
            )
            ->get(
                route(
                    'account-recovery.admin.index'
                )
            )
            ->assertForbidden();
    }

    public function test_system_admin_can_authorize_pending_recovery(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $target =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $recovery =
            $this->pendingRecovery(
                $target
            );

        $this
            ->actingAs(
                $administrator
            )
            ->post(
                route(
                    'account-recovery.admin.approve',
                    $recovery
                )
            )
            ->assertRedirect()
            ->assertSessionHas(
                'status'
            );

        $recovery->refresh();

        $this->assertSame(
            AccountRecoveryRequest::STATUS_APPROVED,
            $recovery->status
        );

        $this->assertSame(
            $administrator->id,
            $recovery->approved_by
        );

        $this->assertNotNull(
            $recovery->approved_at
        );

        $this->assertTrue(
            $recovery->expires_at->isFuture()
        );
    }

    public function test_system_admin_can_reject_pending_recovery(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $target =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $recovery =
            $this->pendingRecovery(
                $target
            );

        $this
            ->actingAs(
                $administrator
            )
            ->post(
                route(
                    'account-recovery.admin.reject',
                    $recovery
                )
            )
            ->assertRedirect()
            ->assertSessionHas(
                'status'
            );

        $this->assertSame(
            AccountRecoveryRequest::STATUS_REJECTED,
            $recovery
                ->fresh()
                ->status
        );
    }

    public function test_system_admin_cannot_approve_own_recovery_request(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $recovery =
            $this->pendingRecovery(
                $administrator
            );

        $this
            ->actingAs(
                $administrator
            )
            ->from(
                route(
                    'account-recovery.admin.index'
                )
            )
            ->post(
                route(
                    'account-recovery.admin.approve',
                    $recovery
                )
            )
            ->assertRedirect(
                route(
                    'account-recovery.admin.index'
                )
            )
            ->assertSessionHasErrors(
                'recovery'
            );

        $this->assertSame(
            AccountRecoveryRequest::STATUS_PENDING,
            $recovery
                ->fresh()
                ->status
        );
    }

    public function test_recovery_notification_links_to_recovery_workspace(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $target =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this->pendingRecovery(
            $target
        );

        $notification =
            AppNotification::query()
                ->where(
                    'recipient_email',
                    $administrator->email
                )
                ->where(
                    'module',
                    'account_recovery'
                )
                ->first();

        $this->assertNotNull(
            $notification
        );

        $this->assertSame(
            route(
                'account-recovery.admin.index'
            ),
            $notification->link
        );
    }

    public function test_staff_accounts_links_to_recovery_workspace(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs(
                $administrator
            )
            ->get(
                route(
                    'users.index'
                )
            )
            ->assertOk()
            ->assertSee(
                'Recovery Requests'
            )
            ->assertSee(
                route(
                    'account-recovery.admin.index'
                ),
                false
            );
    }

    public function test_recovery_workspace_supports_status_filtering(): void
    {
        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $pendingUser =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $approvedUser =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $pending =
            $this->pendingRecovery(
                $pendingUser
            );

        $approved =
            $this->pendingRecovery(
                $approvedUser
            );

        app(
            AccountRecoveryService::class
        )->approve(
            $approved,
            $administrator
        );

        $this
            ->actingAs(
                $administrator
            )
            ->get(
                route(
                    'account-recovery.admin.index',
                    [
                        'status' => AccountRecoveryRequest::STATUS_PENDING,
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                $pending->reference
            )
            ->assertDontSee(
                $approved->reference
            );
    }
}
