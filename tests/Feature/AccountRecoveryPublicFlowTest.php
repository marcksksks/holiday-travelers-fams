<?php

namespace Tests\Feature;

use App\Models\AccountRecoveryRequest;
use App\Models\User;
use App\Services\AccountRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountRecoveryPublicFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'two-factor.safe_devices.enabled' => false,
        ]);

        Cache::flush();
    }

    private function user(
        string $role = User::ROLE_EMPLOYEE
    ): User {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_public_recovery_page_exposes_both_secure_recovery_paths(): void
    {
        $this
            ->get(
                route('password.request')
            )
            ->assertOk()
            ->assertSee(
                'Secure Account Recovery'
            )
            ->assertSee(
                'Request Administrator Recovery'
            )
            ->assertSee(
                'Use Recovery Code'
            )
            ->assertDontSee(
                'Send Reset Link'
            );
    }

    public function test_unknown_account_receives_generic_pending_recovery_experience(): void
    {
        $response =
            $this
                ->post(
                    route(
                        'account-recovery.request'
                    ),
                    [
                        'email' => 'unknown-account@example.test',
                    ]
                );

        $response
            ->assertRedirect(
                route(
                    'account-recovery.status'
                )
            )
            ->assertSessionHas(
                'status'
            );

        $this->assertDatabaseCount(
            'account_recovery_requests',
            0
        );

        $this
            ->get(
                route(
                    'account-recovery.status'
                )
            )
            ->assertOk()
            ->assertSee(
                'Verification pending'
            )
            ->assertSee(
                'Refresh Status'
            );
    }

    public function test_existing_account_can_submit_admin_recovery_without_public_account_disclosure(): void
    {
        $user =
            $this->user();

        $response =
            $this
                ->post(
                    route(
                        'account-recovery.request'
                    ),
                    [
                        'email' => $user->email,
                    ]
                );

        $response
            ->assertRedirect(
                route(
                    'account-recovery.status'
                )
            )
            ->assertSessionHas(
                'status',
                'Recovery request received. If the account is eligible, a System Administrator can review the request.'
            );

        $this->assertDatabaseHas(
            'account_recovery_requests',
            [
                'user_id' => $user->id,

                'status' => AccountRecoveryRequest::STATUS_PENDING,

                'recovery_method' => AccountRecoveryRequest::METHOD_ADMIN,
            ]
        );

        $this
            ->get(
                route(
                    'account-recovery.status'
                )
            )
            ->assertOk()
            ->assertSee(
                'Verification pending'
            );
    }

    public function test_mfa_recovery_code_can_complete_public_password_recovery(): void
    {
        $user =
            $this->user();

        $user->createTwoFactorAuth();

        $this->assertTrue(
            $user->confirmTwoFactorAuth(
                $user->makeTwoFactorCode()
            )
        );

        $code =
            $user
                ->getRecoveryCodes()
                ->pluck('code')
                ->first();

        $this
            ->post(
                route(
                    'account-recovery.recovery-code'
                ),
                [
                    'email' => $user->email,

                    'recovery_code' => $code,
                ]
            )
            ->assertRedirect(
                route(
                    'account-recovery.reset'
                )
            );

        $this
            ->get(
                route(
                    'account-recovery.reset'
                )
            )
            ->assertOk()
            ->assertSee(
                'Create a new password'
            )
            ->assertSee(
                'Recovery Code'
            );

        $this
            ->post(
                route(
                    'account-recovery.update'
                ),
                [
                    'password' => 'RecoveredPublicPassword456!',

                    'password_confirmation' => 'RecoveredPublicPassword456!',
                ]
            )
            ->assertRedirect(
                route('login')
            )
            ->assertSessionHas(
                'status'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'RecoveredPublicPassword456!',
                $user->password
            )
        );

        $this->assertSame(
            AccountRecoveryRequest::STATUS_COMPLETED,
            AccountRecoveryRequest::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->latest('id')
                ->value('status')
        );
    }

    public function test_admin_approval_unlocks_public_reset_page(): void
    {
        $user =
            $this->user();

        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->post(
                route(
                    'account-recovery.request'
                ),
                [
                    'email' => $user->email,
                ]
            )
            ->assertRedirect(
                route(
                    'account-recovery.status'
                )
            );

        $recovery =
            AccountRecoveryRequest::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->firstOrFail();

        app(
            AccountRecoveryService::class
        )->approve(
            $recovery,
            $administrator
        );

        $this
            ->get(
                route(
                    'account-recovery.status'
                )
            )
            ->assertOk()
            ->assertSee(
                'Recovery authorized'
            )
            ->assertSee(
                'Create New Password'
            );

        $this
            ->get(
                route(
                    'account-recovery.reset'
                )
            )
            ->assertOk()
            ->assertSee(
                'Admin Verified'
            );
    }
}
