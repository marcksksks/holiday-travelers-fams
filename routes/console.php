<?php

use App\Services\ContractExpiryService;
use App\Services\DatabaseBackupService;
use App\Services\LegalDeadlineService;
use App\Services\OperationalReminderService;
use App\Services\RetentionReviewService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'retention:review',
    function () {

        $result = app(
            RetentionReviewService::class
        )->run();

        $this->info(
            'Retention review completed.'
        );

        $this->line(
            'Retention records flagged: '.
            $result['retentions_flagged']
        );

        $this->line(
            'Documents marked Needs Review: '.
            $result['documents_flagged']
        );

        $this->line(
            'Notifications processed: '.
            $result['notifications_processed']
        );
    }
)->purpose(
    'Check retention review dates and flag records requiring review.'
);

Schedule::command('retention:review')
    ->dailyAt('00:10')
    ->withoutOverlapping();

Artisan::command(
    'contracts:check-expiry',
    function () {

        $result = app(
            ContractExpiryService::class
        )->run();

        $this->info(
            'Contract expiry check completed.'
        );

        $this->line(
            'Active contracts checked: '.
            $result['contracts_checked']
        );

        $this->line(
            'Notification targets processed: '.
            $result['alerts_processed']
        );

        $this->line(
            'Contracts marked expired: '.
            $result['contracts_expired']
        );
    }
)->purpose(
    'Check active contracts for upcoming expiry dates and expired contracts.'
);

Schedule::command(
    'contracts:check-expiry'
)
    ->dailyAt('00:20')
    ->withoutOverlapping();

Artisan::command(
    'legal:check-deadlines',
    function () {

        $result = app(
            LegalDeadlineService::class
        )->run();

        $this->info(
            'Legal deadline check completed.'
        );

        $this->line(
            'Active legal records checked: '.
            $result['records_checked']
        );

        $this->line(
            'Notification targets processed: '.
            $result['alerts_processed']
        );

        $this->line(
            'Records marked Action Required: '.
            $result['action_required']
        );

        $this->line(
            'Records marked Expired: '.
            $result['expired']
        );
    }
)->purpose(
    'Check legal records for approaching and passed expiration dates.'
);

Schedule::command(
    'legal:check-deadlines'
)
    ->dailyAt('00:30')
    ->withoutOverlapping();

Artisan::command(
    'operations:check-reminders',
    function () {

        $result = app(
            OperationalReminderService::class
        )->run();

        $this->info(
            'Operational reminder check completed.'
        );

        $this->line(
            'Reservations checked: '
            .$result['reservations_checked']
        );

        $this->line(
            'Reservation notification targets: '
            .$result['reservation_alert_targets']
        );

        $this->line(
            'Checked-in visitors checked: '
            .$result['visitors_checked']
        );

        $this->line(
            'Visitor notification targets: '
            .$result['visitor_alert_targets']
        );

    }
)->purpose(
    'Notify operational users when reservations or appointment visitors are within 15 minutes of checkout.'
);

Schedule::command(
    'operations:check-reminders'
)
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command(
    'database:backup {--no-prune : Keep all existing backup archives}',
    function () {
        try {
            $result =
                app(
                    DatabaseBackupService::class
                )->run(
                    ! $this->option(
                        'no-prune'
                    )
                );

            $this->info(
                'PostgreSQL backup completed.'
            );

            $this->line(
                'Archive: '.
                $result['filename']
            );

            $this->line(
                'Size: '.
                $result['size_bytes'].
                ' bytes'
            );

            $this->line(
                'SHA-256: '.
                $result['sha256']
            );

            $this->line(
                'Expired backups pruned: '.
                $result['pruned']
            );

            return 0;
        } catch (Throwable $exception) {
            report(
                $exception
            );

            $this->error(
                'Database backup failed. Review the application log for details.'
            );

            return 1;
        }
    }
)->purpose(
    'Create a verified PostgreSQL custom-format database backup.'
);

if (
    config(
        'backup.database.enabled',
        true
    )
) {
    Schedule::command(
        'database:backup'
    )
        ->dailyAt(
            (string) config(
                'backup.database.schedule_time',
                '01:00'
            )
        )
        ->withoutOverlapping(
            180
        )
        ->onOneServer();
}
