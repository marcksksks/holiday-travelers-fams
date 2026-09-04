<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_containers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('document_containers')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->string('path')->unique();

            $table->string('module', 50)->nullable()->index();

            $table->boolean('is_system')
                ->default(false);

            $table->timestamps();

            $table->index(['parent_id', 'name']);
        });

        Schema::table('archive_documents', function (Blueprint $table) {
            $table->foreignId('container_id')
                ->nullable()
                ->constrained('document_containers')
                ->nullOnDelete();

            $table->string('source_module', 50)
                ->nullable()
                ->index();

            $table->string('system_key', 191)
                ->nullable()
                ->unique();

            $table->boolean('is_system_generated')
                ->default(false)
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('archive_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('container_id');
            $table->dropColumn('source_module');
            $table->dropColumn('system_key');
            $table->dropColumn('is_system_generated');
        });

        Schema::dropIfExists('document_containers');
    }
};