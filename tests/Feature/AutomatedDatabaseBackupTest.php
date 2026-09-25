<?php

namespace Tests\Feature;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AutomatedDatabaseBackupTest extends TestCase
{
    public function test_database_backup_configuration_is_private_and_bounded(): void
    {
        $this->assertTrue(
            (bool) config(
                'backup.database.enabled'
            )
        );

        $this->assertSame(
            '01:00',
            config(
                'backup.database.schedule_time'
            )
        );

        $this->assertSame(
            7,
            config(
                'backup.database.retention_days'
            )
        );

        $this->assertSame(
            300,
            config(
                'backup.database.timeout_seconds'
            )
        );

        $directory =
            (string) config(
                'backup.database.directory'
            );

        $this->assertStringStartsWith(
            storage_path('app'),
            $directory
        );

        $this->assertStringNotContainsString(
            public_path(),
            $directory
        );
    }

    public function test_database_backup_artisan_command_is_registered(): void
    {
        $this->assertArrayHasKey(
            'database:backup',
            Artisan::all()
        );
    }

    public function test_database_backup_is_scheduled_daily(): void
    {
        $events =
            collect(
                app(
                    Schedule::class
                )->events()
            )
                ->filter(
                    fn ($event) => str_contains(
                        (string) $event->command,
                        'database:backup'
                    )
                )
                ->values();

        $this->assertCount(
            1,
            $events
        );

        $this->assertSame(
            '0 1 * * *',
            $events->first()->expression
        );
    }

    public function test_backup_service_uses_argument_array_and_child_password_environment(): void
    {
        $source =
            file_get_contents(
                base_path(
                    'app/Services/DatabaseBackupService.php'
                )
            );

        $this->assertIsString(
            $source
        );

        foreach ([
            'new Process(',
            "'PGPASSWORD'",
            "'--format=custom'",
            "'--compress=9'",
            "'--no-owner'",
            "'--no-privileges'",
            "'--no-password'",
            'hash_file(',
            "'sha256'",
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $source
            );
        }

        $this->assertStringNotContainsString(
            "'--password",
            $source
        );

        $this->assertStringNotContainsString(
            'DB_PASSWORD=',
            $source
        );
    }

    public function test_backup_service_is_resolvable(): void
    {
        $this->assertInstanceOf(
            DatabaseBackupService::class,
            app(
                DatabaseBackupService::class
            )
        );
    }

    public function test_docker_runtime_installs_postgresql_client(): void
    {
        $docker =
            file_get_contents(
                base_path(
                    'Dockerfile'
                )
            );

        $this->assertIsString(
            $docker
        );

        $this->assertStringContainsString(
            'postgresql-client',
            $docker
        );
    }

    public function test_environment_template_documents_backup_controls(): void
    {
        $environment =
            file_get_contents(
                base_path(
                    '.env.example'
                )
            );

        $this->assertIsString(
            $environment
        );

        foreach ([
            'DB_BACKUP_ENABLED=true',
            'DB_BACKUP_TIME=01:00',
            'DB_BACKUP_RETENTION_DAYS=7',
            'DB_BACKUP_TIMEOUT=300',
            'DB_BACKUP_PG_DUMP=pg_dump',
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $environment
            );
        }
    }
}
