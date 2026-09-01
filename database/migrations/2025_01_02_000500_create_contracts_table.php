<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->nullable();
            $table->string('title');
            $table->string('contract_type', 32)->default('partnership');
            // hotel | tour_operator | transportation | supplier | partnership | service | other
            $table->json('parties')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('value', 14, 2)->nullable();
            $table->string('currency', 8)->default('PHP');
            $table->text('description')->nullable();
            $table->string('responsible_officer_email')->nullable();
            $table->string('status', 24)->default('draft');
            // draft | under_review | pending_approval | active | expiring_soon | expired | renewed | terminated
            $table->string('legal_review_status', 20)->default('pending');
            // not_required | pending | in_review | approved | objections
            $table->text('legal_review_notes')->nullable();
            $table->string('legal_reviewed_by')->nullable();
            $table->timestamp('legal_reviewed_at')->nullable();
            $table->string('approval_status', 20)->default('not_submitted');
            // not_submitted | pending | approved | rejected
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->json('renewals')->nullable(); // [{renewed_at, by, new_end_date, note}]
            $table->foreignId('document_id')->nullable()->constrained('archive_documents')->nullOnDelete();
            $table->string('file_uri')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });

        Schema::table('archive_documents', function (Blueprint $table) {
            $table->foreign('linked_contract_id')->references('id')->on('contracts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('archive_documents', function (Blueprint $table) {
            $table->dropForeign(['linked_contract_id']);
        });
        Schema::dropIfExists('contracts');
    }
};
