<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductionSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_supervisor_runs_laravel_scheduler(): void
    {
        $contents = file_get_contents(
            base_path('docker/supervisord.conf')
        );

        $this->assertIsString($contents);

        $this->assertStringContainsString(
            '[program:scheduler]',
            $contents
        );

        $this->assertStringContainsString(
            'command=php artisan schedule:work',
            $contents
        );

        $this->assertStringContainsString(
            'directory=/var/www/html',
            $contents
        );

        $this->assertStringContainsString(
            'autostart=true',
            $contents
        );

        $this->assertStringContainsString(
            'autorestart=true',
            $contents
        );
    }

    public function test_required_background_commands_are_registered(): void
    {
        $contents = file_get_contents(
            base_path('routes/console.php')
        );

        $this->assertIsString($contents);

        foreach ([
            'retention:review',
            'contracts:check-expiry',
            'legal:check-deadlines',
            'operations:check-reminders',
        ] as $command) {
            $this->assertStringContainsString(
                $command,
                $contents
            );
        }

        $this->assertStringContainsString(
            "->dailyAt('00:10')",
            $contents
        );

        $this->assertStringContainsString(
            "->dailyAt('00:20')",
            $contents
        );

        $this->assertStringContainsString(
            "->dailyAt('00:30')",
            $contents
        );

        $this->assertStringContainsString(
            '->everyMinute()',
            $contents
        );

        $this->assertStringContainsString(
            '->withoutOverlapping()',
            $contents
        );
    }

    public function test_background_commands_execute_successfully(): void
    {
        foreach ([
            'retention:review',
            'contracts:check-expiry',
            'legal:check-deadlines',
            'operations:check-reminders',
        ] as $command) {
            $exitCode = Artisan::call($command);

            $this->assertSame(
                0,
                $exitCode,
                "{$command} did not complete successfully."
            );
        }
    }
}