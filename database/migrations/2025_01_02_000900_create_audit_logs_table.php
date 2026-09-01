<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_email')->nullable();
            $table->string('actor_role', 32)->nullable();
            $table->string('action', 32);
            // login|logout|create|update|delete|approve|reject|check_in|check_out|upload|archive|retention_action|role_change|ai_assist
            $table->string('module', 32);
            $table->string('record_label')->nullable();
            $table->string('record_id')->nullable();
            $table->text('details')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['module', 'created_at']);
            $table->index('actor_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
