<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('visitor_organization')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('visitor_contact')->nullable();
            $table->string('visitor_type', 32)->default('guest');
            // customer | business_partner | supplier | government | applicant | guest | other
            $table->string('host_email')->nullable();
            $table->string('host_name')->nullable();
            $table->date('date');
            $table->string('start_time', 5);
            $table->string('end_time', 5)->nullable();
            $table->text('purpose')->nullable();
            $table->foreignId('facility_id')->nullable()->constrained()->nullOnDelete();
            $table->string('facility_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('scheduled');
            // pending | scheduled | confirmed | cancelled | checked_in | completed | no_show
            $table->foreignId('visitor_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
