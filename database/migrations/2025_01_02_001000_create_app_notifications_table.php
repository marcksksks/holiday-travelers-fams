<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_email');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('module', 20)->default('system');
            // facilities|appointments|visitors|documents|retention|legal|contracts|system
            $table->string('severity', 12)->default('info');
            // info|success|warning|critical
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['recipient_email', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
