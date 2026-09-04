<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archive_documents', function (Blueprint $table) {
            $table->foreignId('linked_legal_record_id')
                ->nullable()
                ->constrained('legal_records')
                ->nullOnDelete();

            $table->foreignId('linked_reservation_id')
                ->nullable()
                ->constrained('reservations')
                ->nullOnDelete();

            $table->foreignId('linked_visitor_id')
                ->nullable()
                ->constrained('visitors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('archive_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('linked_legal_record_id');
            $table->dropConstrainedForeignId('linked_reservation_id');
            $table->dropConstrainedForeignId('linked_visitor_id');
        });
    }
};