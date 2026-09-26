<?php

namespace App\Console\Commands;

use App\Services\VisitorForecastDemoDataService;
use Illuminate\Console\Command;
use Throwable;

class VisitorForecastDemoCommand extends Command
{
    protected $signature =
        'visitor-forecast:demo
        {action=status : status, seed, or purge}
        {--confirm : Required before seed or purge}';

    protected $description =
        'Manage clearly marked synthetic visitor forecasting demonstration data.';

    public function handle(
        VisitorForecastDemoDataService $demo
    ): int {
        $action =
            strtolower(
                trim(
                    (string) $this->argument(
                        'action'
                    )
                )
            );

        if (
            ! in_array(
                $action,
                [
                    'status',
                    'seed',
                    'purge',
                ],
                true
            )
        ) {
            $this->error(
                'Action must be status, seed, or purge.'
            );

            return self::FAILURE;
        }

        if (
            in_array(
                $action,
                [
                    'seed',
                    'purge',
                ],
                true
            )
            &&
            ! $this->option(
                'confirm'
            )
        ) {
            $this->error(
                'Use --confirm for seed or purge.'
            );

            return self::FAILURE;
        }

        try {
            $result =
                match ($action) {
                    'seed' => $demo->seed(),

                    'purge' => $demo->purge(),

                    default => $demo->status(),
                };
        } catch (Throwable $exception) {
            report(
                $exception
            );

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }

        if ($action === 'seed') {
            $this->warn(
                'SYNTHETIC DEMONSTRATION DATA ONLY'
            );

            $this->line(
                'The generated records are not client production history.'
            );
        }

        $this->table(
            [
                'Dataset',
                'Records',
            ],
            [
                [
                    'Synthetic visitors',
                    $result['visitors'],
                ],
                [
                    'Synthetic appointments',
                    $result['appointments'],
                ],
                [
                    'Synthetic reservations',
                    $result['reservations'],
                ],
                [
                    'Total synthetic records',
                    $result['total'],
                ],
            ]
        );

        $this->line(
            'Active: '
            .
            (
                $result['active']
                    ? 'YES'
                    : 'NO'
            )
        );

        if (
            array_key_exists(
                'reference_date',
                $result
            )
        ) {
            $this->line(
                'Reference date: '
                .$result['reference_date']
            );
        }

        if (
            array_key_exists(
                'facility_signal_enabled',
                $result
            )
        ) {
            $this->line(
                'Facility-demand signal: '
                .
                (
                    $result[
                        'facility_signal_enabled'
                    ]
                        ? 'ENABLED'
                        : 'SKIPPED - no available facility'
                )
            );
        }

        return self::SUCCESS;
    }
}
