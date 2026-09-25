<?php

namespace App\Services;

use App\Models\PrivacyConsent;
use App\Models\PrivacyRequest;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
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
        private AuditService $audit
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
