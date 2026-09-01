<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('facility_name')->nullable();
            $table->string('requester_email');
            $table->string('requester_name')->nullable();
            $table->date('date');
            $table->string('start_time', 5); // HH:mm
            $table->string('end_time', 5);   // HH:mm
            $table->integer('attendees')->nullable();
            $table->text('purpose')->nullable();
            $table->string('status', 20)->default('pending');
            // pending | approved | rejected | cancelled | completed
            $table->string('decision_by_email')->nullable();
            $table->timestamp('decision_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
