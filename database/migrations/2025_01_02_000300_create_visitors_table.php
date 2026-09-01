<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('organization')->nullable();
            $table->string('visitor_type', 32)->default('guest');
            $table->text('purpose')->nullable();
            $table->string('host_email')->nullable();
            $table->string('host_name')->nullable();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('id_reference')->nullable();
            $table->string('badge_number')->nullable();
            $table->boolean('is_walk_in')->default(false);
            $table->string('status', 20)->default('expected');
            // expected | awaiting_host | declined | checked_in | completed
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('ai_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add the FK from appointments -> visitors now that visitors exists.
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('visitor_id')->references('id')->on('visitors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['visitor_id']);
        });
        Schema::dropIfExists('visitors');
    }
};
