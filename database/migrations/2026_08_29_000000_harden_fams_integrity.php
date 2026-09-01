<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(
                ['facility_id', 'date', 'start_time'],
                'appointments_facility_date_start_index'
            );
        });

        Schema::table('archive_documents', function (Blueprint $table) {
            $table->index(
                ['confidentiality', 'status'],
                'archive_documents_confidentiality_status_index'
            );

            $table->index(
                'linked_contract_id',
                'archive_documents_linked_contract_id_index'
            );
        });

        Schema::table('legal_records', function (Blueprint $table) {
            $table->index(
                ['expiration_date', 'status'],
                'legal_records_expiration_status_index'
            );
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->index(
                ['status', 'end_date'],
                'contracts_status_end_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_facility_date_start_index');
        });

        Schema::table('archive_documents', function (Blueprint $table) {
            $table->dropIndex('archive_documents_confidentiality_status_index');
            $table->dropIndex('archive_documents_linked_contract_id_index');
        });

        Schema::table('legal_records', function (Blueprint $table) {
            $table->dropIndex('legal_records_expiration_status_index');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex('contracts_status_end_date_index');
        });
    }
};