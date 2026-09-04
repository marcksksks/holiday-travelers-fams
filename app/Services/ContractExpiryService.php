<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Contract;

class ContractExpiryService
{
    public function __construct(
        private NotificationService $notifications
    ) {}

    public function run(): array
    {
        $result = [
            'contracts_checked' => 0,
            'alerts_processed' => 0,
            'contracts_expired' => 0,
        ];

        $today = now()->startOfDay();

        Contract::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($contracts) use (
                    &$result,
                    $today
                ) {
                    foreach ($contracts as $contract) {

                        $result['contracts_checked']++;

                        $endDate =
                            $contract->end_date
                                ->copy()
                                ->startOfDay();

                        /*
                         * Automatically mark contracts
                         * expired after their end date.
                         */
                        if ($endDate->lt($today)) {

                            $contract->update([
                                'status' => 'expired',
                            ]);

                            $recipients =
                                $this->notifications
                                    ->usersWithPermission(
                                        'viewContracts'
                                    );

                            $this->notifications
                                ->notifyOnce(
                                    $recipients
                                        ->map(
                                            fn ($user) => [
                                                'recipient_email' =>
                                                    $user->email,

                                                'title' =>
                                                    'Contract Expired',

                                                'body' =>
                                                    "{$contract->title} ({$contract->contract_number}) expired on {$endDate->format('M d, Y')}.",

                                                'module' =>
                                                    'contracts',

                                                'severity' =>
                                                    'error',

                                                'link' =>
                                                    route(
                                                        'contracts.index',
                                                        [],
                                                        false
                                                    ),

                                                'key' =>
                                                    "contract-expired:{$contract->id}:{$endDate->toDateString()}",
                                            ]
                                        )
                                        ->all()
                                );

                            $result[
                                'alerts_processed'
                            ] += $recipients->count();

                            $result[
                                'contracts_expired'
                            ]++;

                            AuditLog::create([
                                'actor_email' =>
                                    'system',

                                'actor_role' =>
                                    'system',

                                'action' =>
                                    'contract_expired',

                                'module' =>
                                    'contracts',

                                'record_label' =>
                                    "Contract - {$contract->contract_number}",

                                'record_id' =>
                                    $contract->id,

                                'details' =>
                                    "Contract automatically marked expired after {$endDate->toDateString()}.",

                                'created_at' =>
                                    now(),
                            ]);

                            continue;
                        }


                        $daysRemaining =
                            (int) $today
                                ->diffInDays(
                                    $endDate
                                );

                        $threshold = null;
                        $title = null;
                        $severity = 'warning';

                        if ($daysRemaining === 0) {

                            $threshold = 0;

                            $title =
                                'Contract Expires Today';

                            $severity =
                                'error';

                        } elseif ($daysRemaining <= 7) {

                            $threshold = 7;

                            $title =
                                'Urgent: Contract Expiring Soon';

                            $severity =
                                'error';

                        } elseif ($daysRemaining <= 15) {

                            $threshold = 15;

                            $title =
                                '15-Day Contract Expiry Reminder';

                        } elseif ($daysRemaining <= 30) {

                            $threshold = 30;

                            $title =
                                'Contract Expiring Within 30 Days';
                        }

                        if ($threshold === null) {
                            continue;
                        }

                        $recipients =
                            $this->notifications
                                ->usersWithPermission(
                                    'viewContracts'
                                );

                        $body =
                            $daysRemaining === 0
                                ? "{$contract->title} ({$contract->contract_number}) expires today."
                                : "{$contract->title} ({$contract->contract_number}) expires in {$daysRemaining} day(s) on {$endDate->format('M d, Y')}.";

                        $this->notifications
                            ->notifyOnce(
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
                                                'contracts',

                                            'severity' =>
                                                $severity,

                                            'link' =>
                                                route(
                                                    'contracts.index',
                                                    [],
                                                    false
                                                ),

                                            /*
                                             * Threshold is part of the
                                             * key. Therefore one user
                                             * can receive at most one
                                             * 30, 15, 7 and 0-day alert
                                             * for this contract/end date.
                                             */
                                            'key' =>
                                                "contract-expiry:{$contract->id}:{$endDate->toDateString()}:{$threshold}",
                                        ]
                                    )
                                    ->all()
                            );

                        $result[
                            'alerts_processed'
                        ] += $recipients->count();
                    }
                }
            );

        return $result;
    }
}