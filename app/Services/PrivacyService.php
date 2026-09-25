<?php

namespace App\Services;

use App\Models\AccountRecoveryRequest;
use App\Models\PrivacyConsent;
use App\Models\PrivacyRequest;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PrivacyService
{
    public const NOTICE_VERSION = '2026-09-24';

    public const PURPOSE_VISITOR_MANAGEMENT =
        'visitor_management';

    public const LAWFUL_BASIS_LEGITIMATE_INTERESTS =
        'legitimate_interests';

    public const LAWFUL_BASES = [
        'consent',
        'contract',
        'legal_obligation',
        'vital_interests',
        'public_authority',
        'legitimate_interests',
    ];

    public function __construct(
        private AuditService $audit,
        private CredentialRevocationService $credentials
    ) {}

    public function recordForUser(
        User $actor,
        User $subject,
        string $purpose,
        string $lawfulBasis,
        string $noticeVersion,
        bool $consentRequired,
        ?bool $granted,
        ?string $source = null,
        ?array $metadata = null
    ): PrivacyConsent {
        return $this->recordConsent(
            $actor,
            $subject,
            null,
            $purpose,
            $lawfulBasis,
            $noticeVersion,
            $consentRequired,
            $granted,
            $source,
            $metadata
        );
    }

    public function recordForVisitor(
        User $actor,
        Visitor $visitor,
        string $purpose,
        string $lawfulBasis,
        string $noticeVersion,
        bool $consentRequired,
        ?bool $granted,
        ?string $source = null,
        ?array $metadata = null
    ): PrivacyConsent {
        return $this->recordConsent(
            $actor,
            null,
            $visitor,
            $purpose,
            $lawfulBasis,
            $noticeVersion,
            $consentRequired,
            $granted,
            $source,
            $metadata
        );
    }

    public function submitRequest(
        User $user,
        string $type,
        ?string $details = null,
        bool $identityVerified = false
    ): PrivacyRequest {
        if (
            ! in_array(
                $type,
                PrivacyRequest::TYPES,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'type' => 'The selected privacy request type is invalid.',
            ]);
        }

        if (
            $details !== null
            && mb_strlen($details) > 4000
        ) {
            throw ValidationException::withMessages([
                'details' => 'Privacy request details may not exceed 4000 characters.',
            ]);
        }

        return DB::transaction(
            function () use (
                $user,
                $type,
                $details,
                $identityVerified
            ): PrivacyRequest {
                $duplicateExists =
                    PrivacyRequest::query()
                        ->where(
                            'user_id',
                            $user->id
                        )
                        ->where(
                            'type',
                            $type
                        )
                        ->whereIn(
                            'status',
                            PrivacyRequest::OPEN_STATUSES
                        )
                        ->exists();

                if ($duplicateExists) {
                    throw ValidationException::withMessages([
                        'type' => 'An unresolved request of this type already exists.',
                    ]);
                }

                $privacyRequest =
                    PrivacyRequest::create([
                        'user_id' => $user->id,

                        'type' => $type,

                        'status' => PrivacyRequest::STATUS_PENDING,

                        'details' => $details,

                        'identity_verified_at' => $identityVerified
                                ? now()
                                : null,

                        'submitted_at' => now(),
                    ]);

                $this->audit->log(
                    $user,
                    'create',
                    'privacy',
                    "Privacy request - {$type}",
                    (string) $privacyRequest->id,
                    'Privacy request submitted'
                );

                return $privacyRequest;
            }
        );
    }

    /**
     * Begin controlled review of a verified privacy request.
     *
     * This phase records governance only.
     * It does not erase or anonymize subject data.
     */
    public function startReview(
        PrivacyRequest $privacyRequest,
        User $reviewer
    ): PrivacyRequest {
        return DB::transaction(
            function () use (
                $privacyRequest,
                $reviewer
            ): PrivacyRequest {
                $lockedRequest =
                    PrivacyRequest::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $privacyRequest->id
                        );

                $this->assertReviewer(
                    $reviewer,
                    $lockedRequest
                );

                if (
                    $lockedRequest
                        ->identity_verified_at === null
                ) {
                    throw ValidationException::withMessages([
                        'privacy_request' => 'Identity verification is required before this privacy request can be reviewed.',
                    ]);
                }

                if (
                    $lockedRequest->status !==
                    PrivacyRequest::STATUS_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'privacy_request' => 'Only pending privacy requests can be moved into review.',
                    ]);
                }

                $lockedRequest->update([
                    'status' => PrivacyRequest::STATUS_UNDER_REVIEW,

                    'reviewed_by_user_id' => $reviewer->id,

                    'reviewed_at' => null,

                    'decision_reason' => null,

                    'retention_basis' => null,

                    'completed_at' => null,
                ]);

                $this->audit->log(
                    $reviewer,
                    'privacy_review_started',
                    'privacy',
                    "Privacy request - {$lockedRequest->type}",
                    (string) $lockedRequest->id,
                    'Privacy request moved to controlled review. No personal data was altered.'
                );

                return $lockedRequest->fresh([
                    'user',
                    'reviewedBy',
                ]);
            }
        );
    }

    /**
     * Record a non-destructive privacy governance decision.
     *
     * Approved requests remain incomplete until controlled
     * execution is performed in Phase 9D.2C.
     */
    public function decideRequest(
        PrivacyRequest $privacyRequest,
        User $reviewer,
        string $decision,
        string $decisionReason,
        ?string $retentionBasis = null
    ): PrivacyRequest {
        $allowedDecisions = [
            PrivacyRequest::STATUS_APPROVED,
            PrivacyRequest::STATUS_PARTIALLY_APPROVED,
            PrivacyRequest::STATUS_DENIED,
        ];

        if (
            ! in_array(
                $decision,
                $allowedDecisions,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'decision' => 'The selected privacy review decision is invalid.',
            ]);
        }

        $decisionReason =
            trim($decisionReason);

        if (
            $decisionReason === ''
            ||
            mb_strlen($decisionReason) > 2000
        ) {
            throw ValidationException::withMessages([
                'decision_reason' => 'A review decision reason of up to 2000 characters is required.',
            ]);
        }

        if ($retentionBasis !== null) {
            $retentionBasis =
                trim($retentionBasis);

            if ($retentionBasis === '') {
                $retentionBasis =
                    null;
            }
        }

        if (
            $retentionBasis !== null
            &&
            mb_strlen($retentionBasis) > 2000
        ) {
            throw ValidationException::withMessages([
                'retention_basis' => 'The retention basis may not exceed 2000 characters.',
            ]);
        }

        if (
            in_array(
                $decision,
                [
                    PrivacyRequest::STATUS_PARTIALLY_APPROVED,
                    PrivacyRequest::STATUS_DENIED,
                ],
                true
            )
            &&
            $retentionBasis === null
        ) {
            throw ValidationException::withMessages([
                'retention_basis' => 'A retention basis is required when a request is partially approved or denied.',
            ]);
        }

        return DB::transaction(
            function () use (
                $privacyRequest,
                $reviewer,
                $decision,
                $decisionReason,
                $retentionBasis
            ): PrivacyRequest {
                $lockedRequest =
                    PrivacyRequest::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $privacyRequest->id
                        );

                $this->assertReviewer(
                    $reviewer,
                    $lockedRequest
                );

                if (
                    $lockedRequest->status !==
                    PrivacyRequest::STATUS_UNDER_REVIEW
                ) {
                    throw ValidationException::withMessages([
                        'privacy_request' => 'Only requests currently under review can receive a decision.',
                    ]);
                }

                $lockedRequest->update([
                    'status' => $decision,

                    'reviewed_by_user_id' => $reviewer->id,

                    'reviewed_at' => now(),

                    'decision_reason' => $decisionReason,

                    'retention_basis' => $retentionBasis,

                    'completed_at' => null,
                ]);

                $this->audit->log(
                    $reviewer,
                    'privacy_decision',
                    'privacy',
                    "Privacy request - {$lockedRequest->type}",
                    (string) $lockedRequest->id,
                    "Privacy governance decision recorded: {$decision}. No personal data was altered."
                );

                return $lockedRequest->fresh([
                    'user',
                    'reviewedBy',
                ]);
            }
        );
    }

    /**
     * Reviewer authorization and separation of duties.
     */
    /**
     * Execute an approved privacy request.
     *
     * Review and execution are intentionally separated.
     */
    public function executeRequest(
        PrivacyRequest $privacyRequest,
        User $executor
    ): PrivacyRequest {
        return DB::transaction(
            function () use (
                $privacyRequest,
                $executor
            ): PrivacyRequest {
                $lockedRequest =
                    PrivacyRequest::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $privacyRequest->id
                        );

                $this->assertExecutor(
                    $executor,
                    $lockedRequest
                );

                if (
                    ! in_array(
                        $lockedRequest->status,
                        [
                            PrivacyRequest::STATUS_APPROVED,
                            PrivacyRequest::STATUS_PARTIALLY_APPROVED,
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'privacy_request' => 'Only approved privacy requests can be executed.',
                    ]);
                }

                $subject =
                    User::query()
                        ->lockForUpdate()
                        ->find(
                            $lockedRequest->user_id
                        );

                if ($subject === null) {
                    throw ValidationException::withMessages([
                        'privacy_request' => 'The privacy request no longer has an available account subject.',
                    ]);
                }

                $decisionStatus =
                    $lockedRequest->status;

                $summary =
                    match ($lockedRequest->type) {
                        PrivacyRequest::TYPE_ERASURE => $this->executeErasure(
                            $subject
                        ),

                        PrivacyRequest::TYPE_BLOCKING => $this->executeBlocking(
                            $subject
                        ),

                        PrivacyRequest::TYPE_WITHDRAW_CONSENT => $this->executeConsentWithdrawal(
                            $subject
                        ),

                        default => throw ValidationException::withMessages([
                            'privacy_request' => 'This privacy request type cannot be executed.',
                        ]),
                    };

                $summary['request_type'] =
                    $lockedRequest->type;

                $summary['review_decision'] =
                    $decisionStatus;

                $summary['structured_execution'] =
                    true;

                $summary['unstructured_records_automatically_modified'] =
                    false;

                $lockedRequest->update([
                    'status' => PrivacyRequest::STATUS_COMPLETED,

                    'executed_by_user_id' => $executor->id,

                    'executed_at' => now(),

                    'execution_summary' => $summary,

                    'completed_at' => now(),
                ]);

                $this->audit->log(
                    $executor,
                    'privacy_execution',
                    'privacy',
                    "Privacy request - {$lockedRequest->type}",
                    (string) $lockedRequest->id,
                    "Controlled privacy execution completed for {$lockedRequest->type}; review decision was {$decisionStatus}."
                );

                return $lockedRequest->fresh([
                    'user',
                    'reviewedBy',
                    'executedBy',
                ]);
            }
        );
    }

    /**
     * Restrict account-based processing and revoke access.
     */
    private function executeBlocking(
        User $subject
    ): array {
        $this->assertAccountMayBeDisabled(
            $subject
        );

        $oldEmail =
            $subject->email;

        $this->credentials
            ->revokeAll(
                $subject
            );

        $resetTokens =
            DB::table(
                'password_reset_tokens'
            )
                ->where(
                    'email',
                    $oldEmail
                )
                ->delete();

        $recoveryRequests =
            AccountRecoveryRequest::query()
                ->where(
                    'user_id',
                    $subject->id
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

                    'request_ip' => null,

                    'user_agent' => null,

                    'expires_at' => now(),

                    'updated_at' => now(),
                ]);

        AccountRecoveryRequest::query()
            ->where(
                'user_id',
                $subject->id
            )
            ->update([
                'request_ip' => null,

                'user_agent' => null,

                'updated_at' => now(),
            ]);

        $subject->forceFill([
            'is_active' => false,

            'force_password_change' => false,

            'privacy_processing_restricted_at' => now(),
        ])->save();

        return [
            'action' => 'processing_restricted',

            'credentials_revoked' => true,

            'password_reset_tokens_removed' => $resetTokens,

            'recovery_authorizations_expired' => $recoveryRequests,

            'business_records_deleted' => 0,
        ];
    }

    /**
     * Withdraw every currently active consent decision for the subject.
     *
     * Notice acknowledgements and non-consent lawful bases are preserved.
     */
    private function executeConsentWithdrawal(
        User $subject
    ): array {
        $withdrawn =
            PrivacyConsent::query()
                ->where(
                    'user_id',
                    $subject->id
                )
                ->where(
                    'consent_required',
                    true
                )
                ->where(
                    'granted',
                    true
                )
                ->whereNull(
                    'withdrawn_at'
                )
                ->update([
                    'withdrawn_at' => now(),

                    'updated_at' => now(),
                ]);

        return [
            'action' => 'consent_withdrawn',

            'consents_withdrawn' => $withdrawn,

            'non_consent_records_preserved' => true,

            'business_records_deleted' => 0,
        ];
    }

    /**
     * Anonymize structured account identity while retaining operational,
     * legal, contractual, retention, and audit records.
     */
    private function executeErasure(
        User $subject
    ): array {
        $this->assertAccountMayBeDisabled(
            $subject
        );

        $oldEmail =
            $subject->email;

        $oldName =
            $subject->full_name;

        $anonymousEmail =
            "anonymized-{$subject->id}@privacy.invalid";

        $anonymousName =
            "Anonymized User #{$subject->id}";

        /*
         * Revoke credentials before changing the login identity.
         */
        $this->credentials
            ->revokeAll(
                $subject
            );

        $resetTokens =
            DB::table(
                'password_reset_tokens'
            )
                ->where(
                    'email',
                    $oldEmail
                )
                ->delete();

        $recoveryRequests =
            AccountRecoveryRequest::query()
                ->where(
                    'user_id',
                    $subject->id
                )
                ->delete();

        if (
            $subject->hasTwoFactorEnabled()
        ) {
            $subject
                ->disableTwoFactorAuth();
        }

        $consentsWithdrawn =
            PrivacyConsent::query()
                ->where(
                    'user_id',
                    $subject->id
                )
                ->where(
                    'consent_required',
                    true
                )
                ->where(
                    'granted',
                    true
                )
                ->whereNull(
                    'withdrawn_at'
                )
                ->update([
                    'withdrawn_at' => now(),

                    'updated_at' => now(),
                ]);

        PrivacyConsent::query()
            ->where(
                'user_id',
                $subject->id
            )
            ->update([
                'metadata' => null,

                'updated_at' => now(),
            ]);

        PrivacyRequest::query()
            ->where(
                'user_id',
                $subject->id
            )
            ->update([
                'details' => null,

                'updated_at' => now(),
            ]);

        $counts = [];

        $counts['reservations_requester'] =
            DB::table(
                'reservations'
            )
                ->where(
                    'requester_email',
                    $oldEmail
                )
                ->update([
                    'requester_email' => $anonymousEmail,

                    'requester_name' => $anonymousName,
                ]);

        $counts['reservations_decider'] =
            DB::table(
                'reservations'
            )
                ->where(
                    'decision_by_email',
                    $oldEmail
                )
                ->update([
                    'decision_by_email' => $anonymousEmail,
                ]);

        $counts['appointment_hosts'] =
            DB::table(
                'appointments'
            )
                ->where(
                    'host_email',
                    $oldEmail
                )
                ->update([
                    'host_email' => $anonymousEmail,

                    'host_name' => $anonymousName,
                ]);

        $counts['visitor_hosts'] =
            DB::table(
                'visitors'
            )
                ->where(
                    'host_email',
                    $oldEmail
                )
                ->update([
                    'host_email' => $anonymousEmail,

                    'host_name' => $anonymousName,
                ]);

        $counts['document_uploaders'] =
            DB::table(
                'archive_documents'
            )
                ->where(
                    'uploaded_by_email',
                    $oldEmail
                )
                ->update([
                    'uploaded_by_email' => $anonymousEmail,
                ]);

        $counts['contract_officers'] =
            DB::table(
                'contracts'
            )
                ->where(
                    'responsible_officer_email',
                    $oldEmail
                )
                ->update([
                    'responsible_officer_email' => $anonymousEmail,
                ]);

        $counts['legal_officers'] =
            DB::table(
                'legal_records'
            )
                ->where(
                    'responsible_officer_email',
                    $oldEmail
                )
                ->update([
                    'responsible_officer_email' => $anonymousEmail,
                ]);

        $counts['legal_assignments_removed'] =
            DB::table(
                'legal_records'
            )
                ->where(
                    'assigned_user_id',
                    $subject->id
                )
                ->update([
                    'assigned_user_id' => null,
                ]);

        $counts['retention_requesters'] =
            DB::table(
                'record_retentions'
            )
                ->where(
                    'disposition_requested_by',
                    $oldEmail
                )
                ->update([
                    'disposition_requested_by' => $anonymousEmail,
                ]);

        $counts['retention_deciders'] =
            DB::table(
                'record_retentions'
            )
                ->where(
                    'disposition_decided_by',
                    $oldEmail
                )
                ->update([
                    'disposition_decided_by' => $anonymousEmail,
                ]);

        /*
         * Notifications are transient user-facing records, unlike
         * operational and audit records.
         */
        $counts['notifications_removed'] =
            DB::table(
                'app_notifications'
            )
                ->where(
                    'recipient_email',
                    $oldEmail
                )
                ->delete();

        /*
         * Preserve audit rows, but replace the direct actor identifier.
         */
        $auditIds =
            DB::table(
                'audit_logs'
            )
                ->where(
                    'actor_email',
                    $oldEmail
                )
                ->pluck(
                    'id'
                )
                ->all();

        $counts['audit_entries_anonymized'] =
            count(
                $auditIds
            );

        if ($auditIds !== []) {
            $this->anonymizeAuditEntries(
                $auditIds,
                $oldEmail,
                $oldName,
                $anonymousEmail,
                $anonymousName
            );
        }

        $subject->forceFill([
            'full_name' => $anonymousName,

            'email' => $anonymousEmail,

            'email_verified_at' => null,

            'password' => Hash::make(
                Str::random(64)
            ),

            'department' => null,

            'job_title' => null,

            'phone' => null,

            'is_active' => false,

            'force_password_change' => false,

            'remember_token' => null,

            'privacy_processing_restricted_at' => now(),

            'privacy_anonymized_at' => now(),
        ])->save();

        return [
            'action' => 'structured_identity_anonymized',

            'credentials_revoked' => true,

            'password_reset_tokens_removed' => $resetTokens,

            'recovery_requests_removed' => $recoveryRequests,

            'consents_withdrawn' => $consentsWithdrawn,

            'structured_reference_updates' => $counts,

            'operational_records_preserved' => true,

            'audit_records_preserved' => true,

            'unstructured_document_contents_modified' => false,
        ];
    }

    /**
     * Preserve audit history while removing the subject's direct identity.
     */
    private function anonymizeAuditEntries(
        array $auditIds,
        string $oldEmail,
        string $oldName,
        string $anonymousEmail,
        string $anonymousName
    ): void {
        $rows =
            DB::table(
                'audit_logs'
            )
                ->whereIn(
                    'id',
                    $auditIds
                )
                ->get([
                    'id',
                    'record_label',
                    'details',
                ]);

        foreach ($rows as $row) {
            $recordLabel =
                $row->record_label;

            $details =
                $row->details;

            if ($recordLabel !== null) {
                $recordLabel =
                    str_replace(
                        [
                            $oldEmail,
                            $oldName,
                        ],
                        [
                            $anonymousEmail,
                            $anonymousName,
                        ],
                        $recordLabel
                    );
            }

            if ($details !== null) {
                $details =
                    str_replace(
                        [
                            $oldEmail,
                            $oldName,
                        ],
                        [
                            $anonymousEmail,
                            $anonymousName,
                        ],
                        $details
                    );
            }

            DB::table(
                'audit_logs'
            )
                ->where(
                    'id',
                    $row->id
                )
                ->update([
                    'actor_email' => $anonymousEmail,

                    'record_label' => $recordLabel,

                    'details' => $details,
                ]);
        }
    }

    private function assertExecutor(
        User $executor,
        PrivacyRequest $privacyRequest
    ): void {
        if (
            ! $executor->is_active
            ||
            ! $executor->can(
                'executePrivacy'
            )
        ) {
            throw ValidationException::withMessages([
                'privacy_request' => 'Only an active System Administrator can execute an approved privacy request.',
            ])->status(403);
        }

        if (
            $privacyRequest->user_id !== null
            &&
            (int) $privacyRequest->user_id ===
                (int) $executor->id
        ) {
            throw ValidationException::withMessages([
                'privacy_request' => 'You cannot execute your own privacy request.',
            ]);
        }

        if (
            $privacyRequest->reviewed_by_user_id === null
        ) {
            throw ValidationException::withMessages([
                'privacy_request' => 'The request must have a recorded reviewer before execution.',
            ]);
        }

        if (
            (int) $privacyRequest->reviewed_by_user_id ===
            (int) $executor->id
        ) {
            throw ValidationException::withMessages([
                'privacy_request' => 'The reviewer and privacy executor must be different users.',
            ]);
        }
    }

    private function assertAccountMayBeDisabled(
        User $subject
    ): void {
        if (
            ! $subject->isSysAdmin()
            ||
            ! $subject->is_active
        ) {
            return;
        }

        $anotherActiveAdministratorExists =
            User::query()
                ->where(
                    'app_role',
                    User::ROLE_SYS_ADMIN
                )
                ->where(
                    'is_active',
                    true
                )
                ->whereKeyNot(
                    $subject->id
                )
                ->exists();

        if (! $anotherActiveAdministratorExists) {
            throw ValidationException::withMessages([
                'privacy_request' => 'The last active System Administrator cannot be blocked or anonymized.',
            ]);
        }
    }

    private function assertReviewer(
        User $reviewer,
        PrivacyRequest $privacyRequest
    ): void {
        if (
            ! $reviewer->is_active
            ||
            ! $reviewer->can(
                'managePrivacy'
            )
        ) {
            throw ValidationException::withMessages([
                'privacy_request' => 'You are not authorized to review privacy requests.',
            ])->status(403);
        }

        if (
            $privacyRequest->user_id !== null
            &&
            $privacyRequest->user_id ===
                $reviewer->id
        ) {
            throw ValidationException::withMessages([
                'privacy_request' => 'You cannot review or decide your own privacy request.',
            ]);
        }
    }

    private function recordConsent(
        User $actor,
        ?User $subject,
        ?Visitor $visitor,
        string $purpose,
        string $lawfulBasis,
        string $noticeVersion,
        bool $consentRequired,
        ?bool $granted,
        ?string $source,
        ?array $metadata
    ): PrivacyConsent {
        if (
            trim($purpose) === ''
            || mb_strlen($purpose) > 100
        ) {
            throw ValidationException::withMessages([
                'purpose' => 'A valid privacy processing purpose is required.',
            ]);
        }

        if (
            ! in_array(
                $lawfulBasis,
                self::LAWFUL_BASES,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'lawful_basis' => 'The selected lawful basis is invalid.',
            ]);
        }

        if (
            trim($noticeVersion) === ''
            || mb_strlen($noticeVersion) > 50
        ) {
            throw ValidationException::withMessages([
                'notice_version' => 'A valid privacy notice version is required.',
            ]);
        }

        if (
            $source !== null
            && mb_strlen($source) > 50
        ) {
            throw ValidationException::withMessages([
                'source' => 'Privacy consent source may not exceed 50 characters.',
            ]);
        }

        if (
            $subject === null
            && $visitor === null
        ) {
            throw ValidationException::withMessages([
                'subject' => 'A user or visitor privacy subject is required.',
            ]);
        }

        if (
            $subject !== null
            && $visitor !== null
        ) {
            throw ValidationException::withMessages([
                'subject' => 'A privacy record may reference only one subject.',
            ]);
        }

        if (
            $lawfulBasis === 'consent'
            && ! $consentRequired
        ) {
            throw ValidationException::withMessages([
                'consent_required' => 'Consent-based processing must require consent.',
            ]);
        }

        if (
            $consentRequired
            && $lawfulBasis !== 'consent'
        ) {
            throw ValidationException::withMessages([
                'lawful_basis' => 'Consent-required processing must use the consent lawful basis.',
            ]);
        }

        if (
            $consentRequired
            && $granted === null
        ) {
            throw ValidationException::withMessages([
                'granted' => 'Consent must be explicitly granted or declined.',
            ]);
        }

        if (! $consentRequired) {
            $granted = null;
        }

        return DB::transaction(
            function () use (
                $actor,
                $subject,
                $visitor,
                $purpose,
                $lawfulBasis,
                $noticeVersion,
                $consentRequired,
                $granted,
                $source,
                $metadata
            ): PrivacyConsent {
                $consent =
                    PrivacyConsent::create([
                        'user_id' => $subject?->id,

                        'visitor_id' => $visitor?->id,

                        'recorded_by_user_id' => $actor->id,

                        'purpose' => $purpose,

                        'lawful_basis' => $lawfulBasis,

                        'notice_version' => $noticeVersion,

                        'consent_required' => $consentRequired,

                        'granted' => $granted,

                        'acknowledged_at' => now(),

                        'granted_at' => $granted === true
                                ? now()
                                : null,

                        'source' => $source,

                        'metadata' => $metadata,
                    ]);

                $recordLabel =
                    $consentRequired
                        ? "Privacy consent - {$purpose}"
                        : "Privacy notice - {$purpose}";

                $this->audit->log(
                    $actor,
                    'create',
                    'privacy',
                    $recordLabel,
                    (string) $consent->id,
                    "Notice {$noticeVersion}; lawful basis: {$lawfulBasis}"
                );

                return $consent;
            }
        );
    }
}
