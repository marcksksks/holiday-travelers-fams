<?php

namespace App\Services;

use App\Models\ArchiveDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class DocumentAccessService
{
    public function __construct(
        private AuditService $audit
    ) {}

    public function signedUrl(
        User $user,
        ArchiveDocument $document
    ): string {
        if (! $document->file_uri) {
            throw ValidationException::withMessages([
                'file_uri' =>
                    'No file attached to this record.',
            ]);
        }

        $this->assertCanAccess(
            $user,
            $document
        );

        $url = URL::temporarySignedRoute(
            'documents.download',
            now()->addMinutes(5),
            [
                'document' => $document->id,
            ]
        );

        $this->audit->log(
            $user,
            'access',
            'documents',
            "Opened - {$document->title}",
            (string) $document->id,
            'Version '.($document->version ?: 1)
        );

        return $url;
    }

    public function scopeAccessible(
        User $user,
        Builder $query
    ): Builder {
        abort_unless(
            $user->can('viewDocuments'),
            403
        );

        /*
         * Hide Visitor documents from users who
         * cannot access Visitor Management.
         */
        if (! $user->can('viewVisitors')) {
            $query
                ->where(function ($q) {
                    $q->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '!=',
                            'visitors'
                        );
                })
                ->whereNull('linked_visitor_id');
        }

        /*
         * Hide Contract documents from users who
         * cannot access Contract Management.
         */
        if (! $user->can('viewContracts')) {
            $query
                ->where(function ($q) {
                    $q->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '!=',
                            'contracts'
                        );
                })
                ->whereNull('linked_contract_id');
        }

        /*
         * Hide Legal documents from users who
         * cannot access Legal Management.
         */
        if (! $user->can('viewLegal')) {
            $query
                ->where(function ($q) {
                    $q->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '!=',
                            'legal'
                        );
                })
                ->whereNull('linked_legal_record_id');
        }

        /*
         * Confidential records require the
         * confidential permission unless the
         * current user owns/uploaded the record.
         */
        if (! $user->can('viewConfidential')) {
            $query->where(
                function ($q) use ($user) {
                    $q->whereNull(
                        'confidentiality'
                    )
                        ->orWhere(
                            'confidentiality',
                            '!=',
                            'confidential'
                        )
                        ->orWhere(
                            'owner_email',
                            $user->email
                        )
                        ->orWhere(
                            'uploaded_by_email',
                            $user->email
                        );
                }
            );
        }

        return $query;
    }
    public function assertCanAccess(
        User $user,
        ArchiveDocument $document
    ): void {
        /*
         * A valid signed URL alone must not grant
         * access to somebody who cannot use
         * Document Management.
         */
        if (! $user->can('viewDocuments')) {
            $this->deny(
                $user,
                $document,
                'Document Management access required.'
            );
        }

        /*
         * Source-module RBAC.
         *
         * This applies to both:
         * - automatically generated records
         * - manually uploaded documents linked
         *   to another system record
         */
        $requirements = [];

        if (
            $document->source_module === 'visitors' ||
            $document->linked_visitor_id
        ) {
            $requirements[] = 'viewVisitors';
        }

        if (
            $document->source_module === 'contracts' ||
            $document->linked_contract_id
        ) {
            $requirements[] = 'viewContracts';
        }

        if (
            $document->source_module === 'legal' ||
            $document->linked_legal_record_id
        ) {
            $requirements[] = 'viewLegal';
        }

        foreach (
            array_unique($requirements)
            as $permission
        ) {
            if (! $user->can($permission)) {
                $this->deny(
                    $user,
                    $document,
                    'You are not authorised to access records from this source module.'
                );
            }
        }

        /*
         * Confidentiality check.
         */
        $level =
            $document->confidentiality
            ?: 'general';

        $isOwner =
            $document->owner_email === $user->email ||
            $document->uploaded_by_email === $user->email;

        $allowed = match ($level) {
            'general' => true,

            /*
             * All users who can enter Document
             * Management may access restricted
             * records unless source-module RBAC
             * blocked them above.
             */
            'restricted' => true,

            'confidential' =>
                $user->can('viewConfidential') ||
                $isOwner,

            default => false,
        };

        if (! $allowed) {
            $this->deny(
                $user,
                $document,
                "Confidentiality: {$level}"
            );
        }
    }

    private function deny(
        User $user,
        ArchiveDocument $document,
        string $reason
    ): never {
        $this->audit->log(
            $user,
            'access_denied',
            'documents',
            "Denied access - {$document->title}",
            (string) $document->id,
            $reason
        );

        throw ValidationException::withMessages([
            'document' =>
                'You are not authorised to access this document.',
        ])->status(403);
    }
}