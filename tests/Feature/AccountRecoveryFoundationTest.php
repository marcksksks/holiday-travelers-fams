<?php

namespace Tests\Feature;

use App\Models\AccountRecoveryRequest;
use App\Models\User;
use App\Services\AccountRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountRecoveryFoundationTest extends TestCase
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

    private function service(): AccountRecoveryService
    {
        return app(
            AccountRecoveryService::class
        );
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

    public function test_admin_recovery_request_stores_only_hashed_claim_token(): void
    {
        $user =
            $this->user();

        $result =
            $this
                ->service()
                ->requestAdminRecovery(
                    $user->email,
                    '127.0.0.1',
                    'Recovery Foundation Test'
                );

        $this->assertNotNull(
            $result
        );

        $recovery =
            $result['request'];

        $claimToken =
            $result['claim_token'];

        $this->assertSame(
            AccountRecoveryRequest::STATUS_PENDING,
            $recovery->status
        );

        $this->assertSame(
            AccountRecoveryRequest::METHOD_ADMIN,
            $recovery->recovery_method
        );

        $this->assertNotSame(
            $claimToken,
            $recovery->claim_token_hash
        );

        $this->assertSame(
            64,
            strlen(
                $recovery->claim_token_hash
            )
        );

        $this->assertTrue(
            $this
                ->service()
                ->verifyClaimToken(
                    $recovery,
                    $claimToken
                )
        );
    }

    public function test_unknown_and_inactive_accounts_do_not_create_recovery_records(): void
    {
        $this->assertNull(
            $this
                ->service()
                ->requestAdminRecovery(
                    'missing@example.test'
                )
        );

        $inactive =
            $this->user();

        $inactive->update([
            'is_active' => false,
        ]);

        $this->assertNull(
            $this
                ->service()
                ->requestAdminRecovery(
                    $inactive->email
                )
        );

        $this->assertDatabaseCount(
            'account_recovery_requests',
            0
        );
    }

    public function test_only_system_administrator_can_approve_recovery(): void
    {
        $user =
            $this->user();

        $result =
            $this
                ->service()
                ->requestAdminRecovery(
                    $user->email
                );

        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        try {
            $this
                ->service()
                ->approve(
                    $result['request'],
                    $manager
                );

            $this->fail(
                'Manager unexpectedly approved account recovery.'
            );
        } catch (ValidationException) {
            $this->assertTrue(true);
        }

        $administrator =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $approved =
            $this
                ->service()
                ->approve(
                    $result['request'],
                    $administrator
                );

        $this->assertSame(
            AccountRecoveryRequest::STATUS_APPROVED,
            $approved->status
        );

        $this->assertSame(
            $administrator->id,
            $approved->approved_by
        );

        $this->assertNotNull(
            $approved->approved_at
        );

        $this->assertTrue(
            $approved->expires_at->isFuture()
        );
    }

    public function test_mfa_recovery_code_creates_short_lived_approved_recovery_and_is_single_use(): void
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

        $result =
            $this
                ->service()
                ->requestWithRecoveryCode(
                    $user->email,
                    $code,
                    '127.0.0.1',
                    'Recovery Code Test'
                );

        $this->assertNotNull(
            $result
        );

        $this->assertSame(
            AccountRecoveryRequest::STATUS_APPROVED,
            $result['request']->status
        );

        $this->assertSame(
            AccountRecoveryRequest::METHOD_RECOVERY_CODE,
            $result['request']->recovery_method
        );

        $this->assertFalse(
            $user
                ->fresh()
                ->validateTwoFactorCode(
                    $code,
                    true
                )
        );
    }

    public function test_successful_admin_recovery_changes_password_revokes_credentials_and_clears_old_mfa(): void
    {
        $user =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $user->createTwoFactorAuth();

        $this->assertTrue(
            $user->confirmTwoFactorAuth(
                $user->makeTwoFactorCode()
            )
        );

        $user->createToken(
            'recovery-foundation-token'
        );

        $result =
            $this
                ->service()
                ->requestAdminRecovery(
                    $user->email
                );

        $approver =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->service()
            ->approve(
                $result['request'],
                $approver
            );

        $updatedUser =
            $this
                ->service()
                ->complete(
                    $result['request'],
                    $result['claim_token'],
                    'RecoveredPassword456!'
                );

        $this->assertTrue(
            Hash::check(
                'RecoveredPassword456!',
                $updatedUser->password
            )
        );

        $this->assertFalse(
            $updatedUser->hasTwoFactorEnabled()
        );

        $this->assertSame(
            0,
            $updatedUser
                ->tokens()
                ->count()
        );

        $this->assertSame(
            AccountRecoveryRequest::STATUS_COMPLETED,
            $result['request']
                ->fresh()
                ->status
        );
    }
}
