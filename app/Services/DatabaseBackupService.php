<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function run(
        bool $prune = true
    ): array {
        $connectionName =
            (string) config(
                'database.default'
            );

        $connection =
            DB::connection(
                $connectionName
            );

        $database =
            $connection->getConfig();

        if (
            ($database['driver'] ?? null) !==
            'pgsql'
        ) {
            throw new RuntimeException(
                'Database backups currently support PostgreSQL only.'
            );
        }

        $directory =
            (string) config(
                'backup.database.directory'
            );

        if ($directory === '') {
            throw new RuntimeException(
                'Database backup directory is not configured.'
            );
        }

        File::ensureDirectoryExists(
            $directory,
            0700,
            true
        );

        $filename =
            'fams-'.
            now()->format(
                'Ymd-His'
            ).
            '-'.
            Str::lower(
                Str::random(8)
            ).
            '.backup';

        $path =
            $directory.
            DIRECTORY_SEPARATOR.
            $filename;

        $binary =
            trim(
                (string) config(
                    'backup.database.pg_dump_binary',
                    'pg_dump'
                )
            );

        if ($binary === '') {
            throw new RuntimeException(
                'pg_dump binary is not configured.'
            );
        }

        $host =
            $this->scalarConfig(
                $database['host'] ?? null,
                '127.0.0.1'
            );

        $port =
            $this->scalarConfig(
                $database['port'] ?? null,
                '5432'
            );

        $name =
            $this->scalarConfig(
                $database['database'] ?? null
            );

        $username =
            $this->scalarConfig(
                $database['username'] ?? null
            );

        $password =
            $this->scalarConfig(
                $database['password'] ?? null
            );

        if (
            $name === '' ||
            $username === ''
        ) {
            throw new RuntimeException(
                'PostgreSQL backup configuration is incomplete.'
            );
        }

        /*
         * Use an argument array rather than a shell command.
         * The password is supplied only to the child process
         * through PGPASSWORD and is never placed on the
         * command line.
         */
        $process =
            new Process(
                [
                    $binary,
                    '--host='.$host,
                    '--port='.$port,
                    '--username='.$username,
                    '--dbname='.$name,
                    '--no-password',
                    '--format=custom',
                    '--compress=9',
                    '--no-owner',
                    '--no-privileges',
                    '--file='.$path,
                ],
                base_path(),
                [
                    'PGPASSWORD' => $password,
                ]
            );

        $timeout =
            max(
                30,
                min(
                    3600,
                    (int) config(
                        'backup.database.timeout_seconds',
                        300
                    )
                )
            );

        $process->setTimeout(
            $timeout
        );

        try {
            $process->mustRun();
        } catch (\Throwable $exception) {
            if (File::exists($path)) {
                File::delete($path);
            }

            throw new RuntimeException(
                'PostgreSQL backup command failed.',
                0,
                $exception
            );
        }

        clearstatcache(
            true,
            $path
        );

        if (
            ! File::exists($path) ||
            File::size($path) <= 0
        ) {
            if (File::exists($path)) {
                File::delete($path);
            }

            throw new RuntimeException(
                'PostgreSQL backup did not produce a valid archive.'
            );
        }

        @chmod(
            $path,
            0600
        );

        $hash =
            hash_file(
                'sha256',
                $path
            );

        if (
            ! is_string($hash) ||
            $hash === ''
        ) {
            File::delete($path);

            throw new RuntimeException(
                'PostgreSQL backup checksum could not be generated.'
            );
        }

        $pruned =
            $prune
                ? $this->pruneOldBackups(
                    $directory,
                    $path
                )
                : 0;

        $result = [
            'path' => $path,

            'filename' => $filename,

            'size_bytes' => File::size($path),

            'sha256' => strtoupper($hash),

            'pruned' => $pruned,
        ];

        Log::info(
            'Database backup completed.',
            [
                'filename' => $result['filename'],

                'size_bytes' => $result['size_bytes'],

                'sha256' => $result['sha256'],

                'pruned' => $result['pruned'],
            ]
        );

        return $result;
    }

    private function pruneOldBackups(
        string $directory,
        string $currentPath
    ): int {
        $retentionDays =
            max(
                1,
                min(
                    3650,
                    (int) config(
                        'backup.database.retention_days',
                        7
                    )
                )
            );

        $threshold =
            now()
                ->subDays(
                    $retentionDays
                )
                ->getTimestamp();

        $deleted =
            0;

        foreach (
            File::files($directory) as $file
        ) {
            $path =
                $file->getPathname();

            if ($path === $currentPath) {
                continue;
            }

            if (
                ! preg_match(
                    '/^fams-[0-9]{8}-[0-9]{6}-[a-z0-9]{8}\.backup$/',
                    $file->getFilename()
                )
            ) {
                continue;
            }

            if (
                $file->getMTime() >=
                $threshold
            ) {
                continue;
            }

            if (File::delete($path)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    private function scalarConfig(
        mixed $value,
        string $default = ''
    ): string {
        if (is_array($value)) {
            $value =
                $value[0] ??
                $default;
        }

        if (
            $value === null ||
            $value === ''
        ) {
            return $default;
        }

        return (string) $value;
    }
}
