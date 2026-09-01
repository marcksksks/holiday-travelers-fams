<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('facility_type', 32)->default('meeting_room');
            // conference_room | meeting_room | training_room | function_room | other
            $table->string('status', 32)->default('available');
            // available | maintenance | unavailable | archived
            $table->json('equipment')->nullable();
            $table->string('updated_by_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
