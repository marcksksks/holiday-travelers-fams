<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'account_recovery_requests',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->string('reference', 32)
                    ->unique();

                /*
                 * The raw claim token is NEVER stored.
                 * Only its SHA-256 hash is persisted.
                 */
                $table
                    ->char('claim_token_hash', 64);

                $table
                    ->string('status', 20)
                    ->default('pending');

                /*
                 * admin          = sys_admin verified recovery
                 * recovery_code = MFA backup code verified
                 */
                $table
                    ->string('recovery_method', 24)
                    ->default('admin');

                $table
                    ->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->string('request_ip', 45)
                    ->nullable();

                $table
                    ->text('user_agent')
                    ->nullable();

                $table
                    ->timestamp('requested_at')
                    ->useCurrent();

                $table
                    ->timestamp('approved_at')
                    ->nullable();

                $table
                    ->timestamp('rejected_at')
                    ->nullable();

                $table
                    ->timestamp('completed_at')
                    ->nullable();

                $table
                    ->timestamp('expires_at')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'user_id',
                    'status',
                ]);

                $table->index([
                    'status',
                    'requested_at',
                ]);

                $table->index(
                    'expires_at'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'account_recovery_requests'
        );
    }
};
