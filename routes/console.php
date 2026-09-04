<?php

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
            \App\Services\RetentionReviewService::class
        )->run();

        $this->info(
            'Retention review completed.'
        );

        $this->line(
            'Retention records flagged: ' .
            $result['retentions_flagged']
        );

        $this->line(
            'Documents marked Needs Review: ' .
            $result['documents_flagged']
        );

        $this->line(
            'Notifications processed: ' .
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
            \App\Services\ContractExpiryService::class
        )->run();

        $this->info(
            'Contract expiry check completed.'
        );

        $this->line(
            'Active contracts checked: ' .
            $result['contracts_checked']
        );

        $this->line(
            'Notification targets processed: ' .
            $result['alerts_processed']
        );

        $this->line(
            'Contracts marked expired: ' .
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
            \App\Services\LegalDeadlineService::class
        )->run();

        $this->info(
            'Legal deadline check completed.'
        );

        $this->line(
            'Active legal records checked: ' .
            $result['records_checked']
        );

        $this->line(
            'Notification targets processed: ' .
            $result['alerts_processed']
        );

        $this->line(
            'Records marked Action Required: ' .
            $result['action_required']
        );

        $this->line(
            'Records marked Expired: ' .
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
