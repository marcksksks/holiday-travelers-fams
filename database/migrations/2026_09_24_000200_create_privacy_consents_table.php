<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_consents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('visitor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('recorded_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('purpose', 100);
            $table->string('lawful_basis', 32);
            $table->string('notice_version', 50);

            $table->boolean('consent_required')
                ->default(false);

            $table->boolean('granted')
                ->nullable();

            $table->timestamp('acknowledged_at')
                ->nullable();

            $table->timestamp('granted_at')
                ->nullable();

            $table->timestamp('withdrawn_at')
                ->nullable();

            $table->string('source', 50)
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'purpose',
            ]);

            $table->index([
                'visitor_id',
                'purpose',
            ]);

            $table->index([
                'purpose',
                'lawful_basis',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_consents');
    }
};
