<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('record_retentions', function (Blueprint $table) {
            $table->string('disposition_status', 30)
                ->nullable()
                ->index();

            $table->string('disposition_requested_by')
                ->nullable();

            $table->timestamp('disposition_requested_at')
                ->nullable();

            $table->text('disposition_reason')
                ->nullable();

            $table->string('disposition_decided_by')
                ->nullable();

            $table->timestamp('disposition_decided_at')
                ->nullable();

            $table->text('disposition_decision_notes')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('record_retentions', function (Blueprint $table) {
            $table->dropColumn([
                'disposition_status',
                'disposition_requested_by',
                'disposition_requested_at',
                'disposition_reason',
                'disposition_decided_by',
                'disposition_decided_at',
                'disposition_decision_notes',
            ]);
        });
    }
};