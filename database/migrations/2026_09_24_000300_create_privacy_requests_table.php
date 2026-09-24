<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('type', 32);

            $table->string('status', 32)
                ->default('pending');

            $table->text('details')
                ->nullable();

            $table->timestamp('identity_verified_at')
                ->nullable();

            $table->timestamp('submitted_at');

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->text('decision_reason')
                ->nullable();

            $table->text('retention_basis')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'type',
                'status',
            ]);

            $table->index([
                'status',
                'submitted_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_requests');
    }
};
