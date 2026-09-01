<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archive_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 32)->default('administrative');
            // administrative | contract | legal | permit | license | compliance | partnership | financial | operational | other
            $table->string('department')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('confidentiality', 20)->default('general');
            // general | restricted | confidential
            $table->date('document_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('status', 20)->default('active');
            // active | needs_review | archived | superseded
            $table->unsignedInteger('version')->default(1);
            $table->string('file_uri')->nullable();
            $table->string('file_name')->nullable();
            $table->string('uploaded_by_email')->nullable();
            $table->foreignId('linked_contract_id')->nullable();
            $table->json('history')->nullable(); // [{version, action, by, at, note}]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_documents');
    }
};
