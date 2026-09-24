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
