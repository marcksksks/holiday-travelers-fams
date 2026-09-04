<?php

namespace App\Services;

use App\Models\ArchiveDocument;
use App\Models\AuditLog;
use App\Models\RecordRetention;
use Illuminate\Support\Facades\DB;

class RetentionReviewService
{
    public function __construct(
        private NotificationService $notifications
    ) {}

    public function run(): array
    {
        $result = [
            'retentions_flagged' => 0,
            'documents_flagged' => 0,
            'notifications_processed' => 0,
        ];

        RecordRetention::query()
            ->whereNotNull('review_date')
            ->whereDate(
                'review_date',
                '<=',
                today()
            )
            ->whereIn(
                'status',
                [
                    'retained',
                    'extended',
                ]
            )
            ->orderBy('id')
            ->chunkById(
                100,
                function ($retentions) use (&$result) {

                    foreach ($retentions as $retention) {

                        DB::transaction(
                            function () use (
                                $retention,
                                &$result
                            ) {

                                $retention->refresh();

                                if (
                                    ! in_array(
                                        $retention->status,
                                        [
                                            'retained',
                                            'extended',
                                        ],
                                        true
                                    )
                                ) {
                                    return;
                                }

                                if (
                                    ! $retention->review_date ||
                                    $retention
                                        ->review_date
                                        ->isFuture()
                                ) {
                                    return;
                                }

                                $reviewDateKey =
                                    $retention
                                        ->review_date
                                        ->toDateString();

                                $complianceStatus =
                                    $retention
                                        ->compliance_status ===
                                        'non_compliant'
                                        ? 'non_compliant'
                                        : 'at_risk';


                                /*
                                 * Flag Retention record.
                                 */
                                $retention->update([
                                    'status' =>
                                        'review_required',

                                    'compliance_status' =>
                                        $complianceStatus,

                                    'last_action_by' =>
                                        'system',

                                    'last_action_at' =>
                                        now(),
                                ]);

                                $result[
                                    'retentions_flagged'
                                ]++;


                                /*
                                 * Notify users responsible for
                                 * retention management.
                                 */
                                $retentionUsers =
                                    $this
                                        ->notifications
                                        ->usersWithPermission(
                                            'manageRetention'
                                        );

                                $this
                                    ->notifications
                                    ->notifyOnce(
                                        $retentionUsers
                                            ->map(
                                                fn ($user) => [
                                                    'recipient_email' =>
                                                        $user->email,

                                                    'title' =>
                                                        'Retention Review Required',

                                                    'body' =>
                                                        "{$retention->record_title} has reached its retention review date.",

                                                    'module' =>
                                                        'retention',

                                                    'severity' =>
                                                        'warning',

                                                    'link' =>
                                                        route(
                                                            'retention.review',
                                                            $retention,
                                                            false
                                                        ),

                                                    'key' =>
                                                        "retention-review:{$retention->id}:{$reviewDateKey}",
                                                ]
                                            )
                                            ->all()
                                    );

                                $result[
                                    'notifications_processed'
                                ] +=
                                    $retentionUsers->count();


                                /*
                                 * Synchronize linked
                                 * Document Management record.
                                 */
                                if (
                                    $retention->record_type ===
                                        'document' &&
                                    $retention->record_id
                                ) {

                                    $document =
                                        ArchiveDocument::find(
                                            $retention->record_id
                                        );

                                    if (
                                        $document &&
                                        $document->status ===
                                            'active'
                                    ) {

                                        $history =
                                            $document->history
                                            ?? [];

                                        $history[] = [
                                            'version' =>
                                                $document->version
                                                ?? 1,

                                            'action' =>
                                                'retention_review_due',

                                            'by' =>
                                                'system',

                                            'at' =>
                                                now()
                                                    ->toISOString(),

                                            'note' =>
                                                'Retention review date reached. Document marked as Needs Review.',
                                        ];

                                        $document->update([
                                            'status' =>
                                                'needs_review',

                                            'history' =>
                                                $history,
                                        ]);

                                        $result[
                                            'documents_flagged'
                                        ]++;


                                        /*
                                         * Only document managers are
                                         * notified here. This avoids
                                         * exposing source-module
                                         * metadata to unauthorized
                                         * users.
                                         */
                                        $documentUsers =
                                            $this
                                                ->notifications
                                                ->usersWithPermission(
                                                    'manageDocuments'
                                                );

                                        $this
                                            ->notifications
                                            ->notifyOnce(
                                                $documentUsers
                                                    ->map(
                                                        fn ($user) => [
                                                            'recipient_email' =>
                                                                $user->email,

                                                            'title' =>
                                                                'Document Needs Review',

                                                            'body' =>
                                                                "{$document->title} requires review because its retention review date has been reached.",

                                                            'module' =>
                                                                'documents',

                                                            'severity' =>
                                                                'warning',

                                                            'link' =>
                                                                route(
                                                                    'documents.show',
                                                                    $document,
                                                                    false
                                                                ),

                                                            'key' =>
                                                                "document-retention-review:{$document->id}:{$reviewDateKey}",
                                                        ]
                                                    )
                                                    ->all()
                                            );

                                        $result[
                                            'notifications_processed'
                                        ] +=
                                            $documentUsers->count();
                                    }
                                }


                                AuditLog::create([
                                    'actor_email' =>
                                        'system',

                                    'actor_role' =>
                                        'system',

                                    'action' =>
                                        'retention_review_due',

                                    'module' =>
                                        'retention',

                                    'record_label' =>
                                        "Retention - {$retention->record_title}",

                                    'record_id' =>
                                        $retention->id,

                                    'details' =>
                                        'Retention review date reached automatically.',

                                    'created_at' =>
                                        now(),
                                ]);
                            }
                        );
                    }
                }
            );

        return $result;
    }
}