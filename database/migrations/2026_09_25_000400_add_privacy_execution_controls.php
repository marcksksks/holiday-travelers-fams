<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table
                    ->timestamp('privacy_processing_restricted_at')
                    ->nullable();

                $table
                    ->timestamp('privacy_anonymized_at')
                    ->nullable();
            }
        );

        Schema::table(
            'privacy_requests',
            function (Blueprint $table): void {
                $table
                    ->foreignId('executed_by_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->timestamp('executed_at')
                    ->nullable();

                $table
                    ->json('execution_summary')
                    ->nullable();

                $table->index([
                    'status',
                    'executed_at',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'privacy_requests',
            function (Blueprint $table): void {
                $table->dropIndex([
                    'status',
                    'executed_at',
                ]);

                $table->dropConstrainedForeignId(
                    'executed_by_user_id'
                );

                $table->dropColumn([
                    'executed_at',
                    'execution_summary',
                ]);
            }
        );

        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'privacy_processing_restricted_at',
                    'privacy_anonymized_at',
                ]);
            }
        );
    }
};
