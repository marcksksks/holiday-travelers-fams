<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Encrypted Laravel payloads are substantially larger than their
         * plaintext values, so searchable VARCHAR fields are intentionally
         * excluded and encrypted fields are widened to TEXT.
         */
        Schema::table('users', function (Blueprint $table): void {
            $table->text('phone')->nullable()->change();
        });

        Schema::table('visitors', function (Blueprint $table): void {
            $table->text('id_reference')->nullable()->change();
        });

        Schema::table(
            'account_recovery_requests',
            function (Blueprint $table): void {
                $table->text('request_ip')->nullable()->change();
            }
        );

        if (
            DB::connection()
                ->getDriverName() === 'pgsql'
        ) {
            DB::statement(
                'ALTER TABLE privacy_consents
                 ALTER COLUMN metadata
                 TYPE text
                 USING metadata::text'
            );

            DB::statement(
                'ALTER TABLE privacy_requests
                 ALTER COLUMN execution_summary
                 TYPE text
                 USING execution_summary::text'
            );
        } else {
            Schema::table(
                'privacy_consents',
                function (Blueprint $table): void {
                    $table->text('metadata')
                        ->nullable()
                        ->change();
                }
            );

            Schema::table(
                'privacy_requests',
                function (Blueprint $table): void {
                    $table->text('execution_summary')
                        ->nullable()
                        ->change();
                }
            );
        }

        $encrypt =
            static function (
                string $table,
                string $column
            ): void {
                DB::table($table)
                    ->whereNotNull($column)
                    ->orderBy('id')
                    ->chunkById(
                        100,
                        function ($rows) use (
                            $table,
                            $column
                        ): void {
                            foreach ($rows as $row) {
                                $value =
                                    $row->{$column};

                                if (
                                    is_array($value)
                                    ||
                                    is_object($value)
                                ) {
                                    $value =
                                        json_encode(
                                            $value,
                                            JSON_THROW_ON_ERROR
                                        );
                                }

                                DB::table($table)
                                    ->where(
                                        'id',
                                        $row->id
                                    )
                                    ->update([
                                        $column => Crypt::encryptString(
                                            (string) $value
                                        ),
                                    ]);
                            }
                        },
                        'id'
                    );
            };

        $encrypt(
            'users',
            'phone'
        );

        $encrypt(
            'visitors',
            'id_reference'
        );

        $encrypt(
            'account_recovery_requests',
            'request_ip'
        );

        $encrypt(
            'account_recovery_requests',
            'user_agent'
        );

        $encrypt(
            'privacy_consents',
            'metadata'
        );

        $encrypt(
            'privacy_requests',
            'details'
        );

        $encrypt(
            'privacy_requests',
            'decision_reason'
        );

        $encrypt(
            'privacy_requests',
            'retention_basis'
        );

        $encrypt(
            'privacy_requests',
            'execution_summary'
        );
    }

    public function down(): void
    {
        /*
         * Rollback decrypts values before restoring the original column
         * types. A wrong APP_KEY must fail loudly instead of silently
         * corrupting protected data.
         */
        $decrypt =
            static function (
                string $table,
                string $column
            ): void {
                DB::table($table)
                    ->whereNotNull($column)
                    ->orderBy('id')
                    ->chunkById(
                        100,
                        function ($rows) use (
                            $table,
                            $column
                        ): void {
                            foreach ($rows as $row) {
                                DB::table($table)
                                    ->where(
                                        'id',
                                        $row->id
                                    )
                                    ->update([
                                        $column => Crypt::decryptString(
                                            (string) $row->{$column}
                                        ),
                                    ]);
                            }
                        },
                        'id'
                    );
            };

        $decrypt(
            'users',
            'phone'
        );

        $decrypt(
            'visitors',
            'id_reference'
        );

        $decrypt(
            'account_recovery_requests',
            'request_ip'
        );

        $decrypt(
            'account_recovery_requests',
            'user_agent'
        );

        $decrypt(
            'privacy_consents',
            'metadata'
        );

        $decrypt(
            'privacy_requests',
            'details'
        );

        $decrypt(
            'privacy_requests',
            'decision_reason'
        );

        $decrypt(
            'privacy_requests',
            'retention_basis'
        );

        $decrypt(
            'privacy_requests',
            'execution_summary'
        );

        if (
            DB::connection()
                ->getDriverName() === 'pgsql'
        ) {
            DB::statement(
                'ALTER TABLE privacy_consents
                 ALTER COLUMN metadata
                 TYPE json
                 USING metadata::json'
            );

            DB::statement(
                'ALTER TABLE privacy_requests
                 ALTER COLUMN execution_summary
                 TYPE json
                 USING execution_summary::json'
            );
        } else {
            Schema::table(
                'privacy_consents',
                function (Blueprint $table): void {
                    $table->json('metadata')
                        ->nullable()
                        ->change();
                }
            );

            Schema::table(
                'privacy_requests',
                function (Blueprint $table): void {
                    $table->json('execution_summary')
                        ->nullable()
                        ->change();
                }
            );
        }

        Schema::table(
            'account_recovery_requests',
            function (Blueprint $table): void {
                $table
                    ->string(
                        'request_ip',
                        45
                    )
                    ->nullable()
                    ->change();
            }
        );

        Schema::table(
            'visitors',
            function (Blueprint $table): void {
                $table
                    ->string(
                        'id_reference'
                    )
                    ->nullable()
                    ->change();
            }
        );

        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table
                    ->string(
                        'phone'
                    )
                    ->nullable()
                    ->change();
            }
        );
    }
};
