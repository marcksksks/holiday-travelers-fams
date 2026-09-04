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
