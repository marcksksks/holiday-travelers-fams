<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legal_records', function (Blueprint $table) {
            $table->string('legal_category', 100)->nullable();
            $table->string('jurisdiction', 150)->nullable();
            $table->text('legal_basis')->nullable();

            $table->string('priority', 20)->default('medium');
            $table->string('confidentiality_level', 20)->default('internal');

            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('due_date')->nullable();
            $table->date('next_action_date')->nullable();
            $table->string('next_action', 500)->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->index(['priority', 'status']);
            $table->index('due_date');
            $table->index('next_action_date');
        });
    }

    public function down(): void
    {
        Schema::table('legal_records', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);

            $table->dropIndex(['priority', 'status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['next_action_date']);

            $table->dropColumn([
                'legal_category',
                'jurisdiction',
                'legal_basis',
                'priority',
                'confidentiality_level',
                'assigned_user_id',
                'due_date',
                'next_action_date',
                'next_action',
                'closed_at',
            ]);
        });
    }
};
