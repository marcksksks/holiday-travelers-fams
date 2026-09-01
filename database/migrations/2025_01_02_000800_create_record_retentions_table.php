<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_retentions', function (Blueprint $table) {
            $table->id();
            $table->string('record_title');
            $table->string('record_type', 20)->default('document');
            // document | contract | legal_record | other
            $table->unsignedBigInteger('record_id')->nullable(); // polymorphic-by-convention (paired with record_type)
            $table->foreignId('policy_id')->nullable()->constrained('retention_policies')->nullOnDelete();
            $table->string('policy_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('review_date')->nullable();
            $table->string('status', 24)->default('retained');
            // retained | review_required | extended | archived | marked_for_disposal
            $table->string('compliance_status', 20)->default('compliant');
            // compliant | at_risk | non_compliant
            $table->string('last_action_by')->nullable();
            $table->timestamp('last_action_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_retentions');
    }
};
