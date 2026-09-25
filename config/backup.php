<?php

return [
    'database' => [
        /*
         * The scheduled backup may be disabled per environment
         * without removing the manual Artisan command.
         */
        'enabled' => env(
            'DB_BACKUP_ENABLED',
            true
        ),

        /*
         * Uses the application timezone.
         */
        'schedule_time' => env(
            'DB_BACKUP_TIME',
            '01:00'
        ),

        /*
         * Successful backup files older than this period
         * are pruned after a new backup is verified.
         */
        'retention_days' => (int) env(
            'DB_BACKUP_RETENTION_DAYS',
            7
        ),

        /*
         * Maximum time allowed for pg_dump.
         */
        'timeout_seconds' => (int) env(
            'DB_BACKUP_TIMEOUT',
            300
        ),

        /*
         * Override this only when pg_dump is not on PATH.
         */
        'pg_dump_binary' => env(
            'DB_BACKUP_PG_DUMP',
            'pg_dump'
        ),

        /*
         * Backups remain outside public web storage.
         */
        'directory' => storage_path(
            'app/backups/database'
        ),
    ],
];
