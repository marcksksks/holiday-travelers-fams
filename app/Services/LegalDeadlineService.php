<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\LegalRecord;

class LegalDeadlineService
{
    public function __construct(
        private NotificationService $notifications
    ) {}

    public function run(): array
    {
        $result = [
            'records_checked' => 0,
            'alerts_processed' => 0,
            'action_required' => 0,
            'expired' => 0,
        ];

        $today = now()->startOfDay();

        LegalRecord::query()
            ->where('status', 'active')
            ->whereNotNull('expiration_date')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($records) use (
                    &$result,
                    $today
                ) {
                    foreach ($records as $record) {

                        $result['records_checked']++;

                        $expiration =
                            $record->expiration_date
                                ->copy()
                                ->startOfDay();

                        $daysRemaining =
                            $expiration->lt($today)
                                ? -1
                                : (int) $today
                                    ->diffInDays($expiration);

                        /*
                         * Already expired.
                         */
                        if ($expiration->lt($today)) {

                            $record->update([
                                'status' =>
                                    'expired',

                                'review_status' =>
                                    'action_required',
                            ]);

                            $this->notify(
                                $record,
                                'Legal Record Expired',
                                "{$record->title} expired on {$expiration->format('M d, Y')} and requires action.",
                                'error',
                                "expired:{$expiration->toDateString()}"
                            );

                            $result['expired']++;
                            $result['action_required']++;

                            $this->audit(
                                $record,
                                'Legal record automatically marked expired.'
                            );

                            continue;
                        }


                        /*
                         * Deadline thresholds.
                         */
                        if ($daysRemaining > 30) {
                            continue;
                        }

                        if ($daysRemaining <= 7) {

                            $threshold = 7;
                            $title =
                                'Urgent Legal Deadline';

                            $severity =
                                'error';

                        } elseif ($daysRemaining <= 15) {

                            $threshold = 15;
                            $title =
                                '15-Day Legal Deadline Reminder';

                            $severity =
                                'warning';

                        } else {

                            $threshold = 30;
                            $title =
                                'Legal Record Expiring Within 30 Days';

                            $severity =
                                'warning';
                        }

                        /*
                         * Approaching expiration requires
                         * legal attention.
                         */
                        if (
                            $record->review_status !==
                            'action_required'
                        ) {
                            $record->update([
                                'review_status' =>
                                    'action_required',
                            ]);

                            $result['action_required']++;
                        }

                        $body =
                            $daysRemaining === 0
                                ? "{$record->title} expires today and requires legal action."
                                : "{$record->title} expires in {$daysRemaining} day(s) on {$expiration->format('M d, Y')}.";

                        $result['alerts_processed'] +=
                            $this->notify(
                                $record,
                                $title,
                                $body,
                                $severity,
                                "threshold:{$threshold}:{$expiration->toDateString()}"
                            );
                    }
                }
            );

        return $result;
    }


    private function notify(
        LegalRecord $record,
        string $title,
        string $body,
        string $severity,
        string $eventKey
    ): int {
        $recipients =
            $this->notifications
                ->usersWithPermission(
                    'viewLegal'
                );

        $this->notifications->notifyOnce(
            $recipients
                ->map(
                    fn ($user) => [
                        'recipient_email' =>
                            $user->email,

                        'title' =>
                            $title,

                        'body' =>
                            $body,

                        'module' =>
                            'legal',

                        'severity' =>
                            $severity,

                        'link' =>
                            route(
                                'legal.index',
                                [],
                                false
                            ),

                        'key' =>
                            "legal-deadline:{$record->id}:{$eventKey}",
                    ]
                )
                ->all()
        );

        return $recipients->count();
    }


    private function audit(
        LegalRecord $record,
        string $details
    ): void {
        AuditLog::create([
            'actor_email' =>
                'system',

            'actor_role' =>
                'system',

            'action' =>
                'legal_deadline',

            'module' =>
                'legal',

            'record_label' =>
                "Legal • {$record->title}",

            'record_id' =>
                $record->id,

            'details' =>
                $details,

            'created_at' =>
                now(),
        ]);
    }
}