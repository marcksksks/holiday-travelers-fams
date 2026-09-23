<?php

namespace App\Services;

use App\Models\AccountRecoveryRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AccountRecoveryService
{
    private const ADMIN_REQUEST_LIFETIME_HOURS =
        24;

    private const RESET_AUTHORIZATION_MINUTES =
        15;

    public function __construct(
        private CredentialRevocationService $credentials,
        private NotificationService $notifications
    ) {}

    /**
     * Create an administrator-assisted recovery request.
     *
     * Unknown or inactive users intentionally return null so the
     * public controller can always show the same generic response.
     *
     * @return array{
     *     request: AccountRecoveryRequest,
     *     claim_token: string
     * }|null
     */
    public function requestAdminRecovery(
        string $email,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ?array {
        $user =
            $this->findActiveUser(
                $email
            );

        if ($user === null) {
            return null;
        }

        $this->expireStaleRequests(
            $user
        );

        $claimToken =
            Str::random(64);

        $recovery =
            AccountRecoveryRequest::create([
                'user_id' => $user->id,

                'reference' => $this->makeReference(),

                'claim_token_hash' => $this->hashClaimToken(
                    $claimToken
                ),

                'status' => AccountRecoveryRequest::STATUS_PENDING,

                'recovery_method' => AccountRecoveryRequest::METHOD_ADMIN,

                'request_ip' => $ipAddress,

                'user_agent' => $userAgent !== null
                        ? mb_substr(
                            $userAgent,
                            0,
                            1000
                        )
                        : null,

                'requested_at' => now(),

                'expires_at' => now()->addHours(
                    self::ADMIN_REQUEST_LIFETIME_HOURS
                ),
            ]);

        $this->notifySystemAdministrators(
            $user,
            $recovery
        );

        $this->auditGuestEvent(
            $recovery,
            'recovery_request',
            'Administrator-assisted account recovery requested'
        );

        return [
            'request' => $recovery,
            'claim_token' => $claimToken,
        ];
    }

    /**
     * Verify a Laragear single-use MFA recovery code and create
     * an immediately approved short-lived reset authorization.
     *
     * @return array{
     *     request: AccountRecoveryRequest,
     *     claim_token: string
     * }|null
     */
    public function requestWithRecoveryCode(
        string $email,
        string $recoveryCode,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ?array {
        $user =
            $this->findActiveUser(
                $email
            );

        if ($user === null) {
            return null;
        }

        if (! $user->hasTwoFactorEnabled()) {
            return null;
        }

        $recoveryCode =
            trim(
                $recoveryCode
            );

        $minimumLength =
            (int) config(
                'two-factor.recovery.length',
                8
            );

        if (
            mb_strlen($recoveryCode)
            <
            $minimumLength
        ) {
            return null;
        }

        if (
            ! $user->validateTwoFactorCode(
                $recoveryCode,
                true
            )
        ) {
            return null;
        }

        $this->expireStaleRequests(
            $user
        );

        $claimToken =
            Str::random(64);

        $recovery =
            AccountRecoveryRequest::create([
                'user_id' => $user->id,

                'reference' => $this->makeReference(),

                'claim_token_hash' => $this->hashClaimToken(
                    $claimToken
                ),

                'status' => AccountRecoveryRequest::STATUS_APPROVED,

                'recovery_method' => AccountRecoveryRequest::METHOD_RECOVERY_CODE,

                'request_ip' => $ipAddress,

                'user_agent' => $userAgent !== null
                        ? mb_substr(
                            $userAgent,
                            0,
                            1000
                        )
                        : null,

                'requested_at' => now(),

                'approved_at' => now(),

                'expires_at' => now()->addMinutes(
                    self::RESET_AUTHORIZATION_MINUTES
                ),
            ]);

        $this->auditUserEvent(
            $user,
            $recovery,
            'recovery_code_verified',
            'Single-use MFA recovery code verified for account recovery'
        );

        return [
            'request' => $recovery,
            'claim_token' => $claimToken,
        ];
    }

    public function approve(
        AccountRecoveryRequest $recovery,
        User $administrator
    ): AccountRecoveryRequest {
        $this->assertSystemAdministrator(
            $administrator
        );

        if (
            (int) $recovery->user_id
            ===
            (int) $administrator->id
        ) {
            throw ValidationException::withMessages([
                'recovery' => 'A System Administrator cannot approve their own account recovery request. Another System Administrator must authorize it.',
            ]);
        }

        $recovery->refresh();

        if ($recovery->hasExpired()) {
            $recovery->update([
                'status' => AccountRecoveryRequest::STATUS_EXPIRED,
            ]);

            throw ValidationException::withMessages([
                'recovery' => 'This recovery request has expired.',
            ]);
        }

        if (! $recovery->isPending()) {
            throw ValidationException::withMessages([
                'recovery' => 'Only pending recovery requests can be approved.',
            ]);
        }

        $recovery->update([
            'status' => AccountRecoveryRequest::STATUS_APPROVED,

            'approved_by' => $administrator->id,

            'approved_at' => now(),

            'expires_at' => now()->addMinutes(
                self::RESET_AUTHORIZATION_MINUTES
            ),
        ]);

        $this->auditAdministratorEvent(
            $administrator,
            $recovery,
            'recovery_approve',
            'Approved administrator-assisted account recovery'
        );

        return $recovery->fresh();
    }

    public function reject(
        AccountRecoveryRequest $recovery,
        User $administrator
    ): AccountRecoveryRequest {
        $this->assertSystemAdministrator(
            $administrator
        );

        $recovery->refresh();

        if (! $recovery->isPending()) {
            throw ValidationException::withMessages([
                'recovery' => 'Only pending recovery requests can be rejected.',
            ]);
        }

        $recovery->update([
            'status' => AccountRecoveryRequest::STATUS_REJECTED,

            'approved_by' => $administrator->id,

            'rejected_at' => now(),

            'expires_at' => now(),
        ]);

        $this->auditAdministratorEvent(
            $administrator,
            $recovery,
            'recovery_reject',
            'Rejected administrator-assisted account recovery'
        );

        return $recovery->fresh();
    }

    public function verifyClaimToken(
        AccountRecoveryRequest $recovery,
        string $claimToken
    ): bool {
        $candidate =
            $this->hashClaimToken(
                trim(
                    $claimToken
                )
            );

        return hash_equals(
            $recovery->claim_token_hash,
            $candidate
        );
    }

    public function complete(
        AccountRecoveryRequest $recovery,
        string $claimToken,
        string $newPassword
    ): User {
        $recovery->refresh();

        if (! $recovery->canReset()) {
            throw ValidationException::withMessages([
                'recovery' => 'This recovery authorization is invalid or has expired.',
            ]);
        }

        if (
            ! $this->verifyClaimToken(
                $recovery,
                $claimToken
            )
        ) {
            throw ValidationException::withMessages([
                'recovery' => 'This recovery authorization is invalid or has expired.',
            ]);
        }

        $user =
            $recovery
                ->user()
                ->firstOrFail();

        DB::transaction(
            function () use (
                $recovery,
                $user,
                $newPassword
            ): void {
                $user->forceFill([
                    'password' => Hash::make(
                        $newPassword
                    ),

                    'force_password_change' => false,
                ])->save();

                /*
                 * Admin-assisted recovery is also the escape hatch
                 * for a lost authenticator and depleted recovery
                 * codes. Remove the old MFA enrollment.
                 *
                 * Privileged roles are already protected by the
                 * RequirePrivilegedMfa middleware and will be sent
                 * to Settings to configure MFA again after login.
                 */
                if (
                    $recovery->recovery_method
                    ===
                    AccountRecoveryRequest::METHOD_ADMIN
                ) {
                    if (
                        $user
                            ->twoFactorAuth()
                            ->exists()
                    ) {
                        $user
                            ->disableTwoFactorAuth();
                    }
                }

                $this->credentials
                    ->revokeAll(
                        $user
                    );

                $recovery->update([
                    'status' => AccountRecoveryRequest::STATUS_COMPLETED,

                    'completed_at' => now(),

                    'expires_at' => now(),
                ]);

                /*
                 * Invalidate every other outstanding reset attempt
                 * for this user once recovery succeeds.
                 */
                AccountRecoveryRequest::query()
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'id',
                        '<>',
                        $recovery->id
                    )
                    ->whereIn(
                        'status',
                        [
                            AccountRecoveryRequest::STATUS_PENDING,
                            AccountRecoveryRequest::STATUS_APPROVED,
                        ]
                    )
                    ->update([
                        'status' => AccountRecoveryRequest::STATUS_EXPIRED,

                        'expires_at' => now(),

                        'updated_at' => now(),
                    ]);

                $this->auditUserEvent(
                    $user,
                    $recovery,
                    'recovery_complete',
                    $recovery->recovery_method
                    ===
                    AccountRecoveryRequest::METHOD_ADMIN
                        ? 'Password recovered; credentials revoked and previous MFA enrollment cleared'
                        : 'Password recovered with MFA recovery code; credentials revoked'
                );
            }
        );

        return $user->fresh();
    }

    private function findActiveUser(
        string $email
    ): ?User {
        $normalized =
            mb_strtolower(
                trim(
                    $email
                )
            );

        if ($normalized === '') {
            return null;
        }

        return User::query()
            ->whereRaw(
                'LOWER(email) = ?',
                [$normalized]
            )
            ->where(
                'is_active',
                true
            )
            ->first();
    }

    private function expireStaleRequests(
        User $user
    ): void {
        AccountRecoveryRequest::query()
            ->where(
                'user_id',
                $user->id
            )
            ->whereIn(
                'status',
                [
                    AccountRecoveryRequest::STATUS_PENDING,
                    AccountRecoveryRequest::STATUS_APPROVED,
                ]
            )
            ->whereNotNull(
                'expires_at'
            )
            ->where(
                'expires_at',
                '<',
                now()
            )
            ->update([
                'status' => AccountRecoveryRequest::STATUS_EXPIRED,

                'updated_at' => now(),
            ]);
    }

    private function assertSystemAdministrator(
        User $administrator
    ): void {
        if (! $administrator->isSysAdmin()) {
            throw ValidationException::withMessages([
                'recovery' => 'Only a System Administrator can authorize account recovery.',
            ]);
        }
    }

    private function makeReference(): string
    {
        do {
            $reference =
                'REC-'.
                now()->format(
                    'Ymd'
                ).
                '-'.
                Str::upper(
                    Str::random(8)
                );
        } while (
            AccountRecoveryRequest::query()
                ->where(
                    'reference',
                    $reference
                )
                ->exists()
        );

        return $reference;
    }

    private function hashClaimToken(
        string $claimToken
    ): string {
        return hash(
            'sha256',
            $claimToken
        );
    }

    private function notifySystemAdministrators(
        User $user,
        AccountRecoveryRequest $recovery
    ): void {
        $administrators =
            $this->notifications
                ->usersByRole(
                    User::ROLE_SYS_ADMIN
                );

        $items = [];

        foreach ($administrators as $administrator) {
            $items[] = [
                'recipient_email' => $administrator->email,

                'title' => 'Account recovery request',

                'body' => $user->full_name.
                    ' submitted an administrator-assisted account recovery request. Reference: '.
                    $recovery->reference,

                'module' => 'account_recovery',

                'severity' => 'warning',

                /*
                 * The dedicated recovery workspace will replace
                 * this Staff Accounts destination in Phase 8D.6C.
                 */
                'link' => route('account-recovery.admin.index'),
            ];
        }

        if ($items !== []) {
            $this->notifications
                ->notify(
                    $items
                );
        }
    }

    private function auditGuestEvent(
        AccountRecoveryRequest $recovery,
        string $action,
        string $details
    ): void {
        AuditLog::create([
            'actor_email' => null,
            'actor_role' => 'guest',
            'action' => $action,
            'module' => 'account_recovery',
            'record_label' => 'Recovery '.$recovery->reference,
            'record_id' => (string) $recovery->id,
            'details' => $details,
            'created_at' => now(),
        ]);
    }

    private function auditUserEvent(
        User $user,
        AccountRecoveryRequest $recovery,
        string $action,
        string $details
    ): void {
        AuditLog::create([
            'actor_email' => $user->email,
            'actor_role' => $user->app_role,
            'action' => $action,
            'module' => 'account_recovery',
            'record_label' => 'Recovery '.$recovery->reference,
            'record_id' => (string) $recovery->id,
            'details' => $details,
            'created_at' => now(),
        ]);
    }

    private function auditAdministratorEvent(
        User $administrator,
        AccountRecoveryRequest $recovery,
        string $action,
        string $details
    ): void {
        AuditLog::create([
            'actor_email' => $administrator->email,
            'actor_role' => $administrator->app_role,
            'action' => $action,
            'module' => 'account_recovery',
            'record_label' => 'Recovery '.$recovery->reference,
            'record_id' => (string) $recovery->id,
            'details' => $details,
            'created_at' => now(),
        ]);
    }
}
