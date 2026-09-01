<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('record_type', 24)->default('permit');
            // permit | license | legal_case | requirement | legal_document
            $table->string('reference_number')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('status', 20)->default('active');
            // active | pending | expiring_soon | expired | renewed | closed
            $table->string('responsible_officer_email')->nullable();
            $table->foreignId('document_id')->nullable()->constrained('archive_documents')->nullOnDelete();
            $table->string('file_uri')->nullable();
            $table->string('file_name')->nullable();
            $table->string('review_status', 20)->default('not_reviewed');
            // not_reviewed | in_review | reviewed | action_required
            $table->text('legal_notes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_records');
    }
};
