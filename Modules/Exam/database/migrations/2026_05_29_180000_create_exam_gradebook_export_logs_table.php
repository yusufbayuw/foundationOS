<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_gradebook_export_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_result_id')->nullable()->constrained('exam_definition_results')->nullOnDelete();
            $table->foreignUuid('exam_participant_id')->nullable()->constrained('exam_participants')->nullOnDelete();
            $table->string('target_module');
            $table->string('target_reference_type')->nullable();
            $table->unsignedBigInteger('target_reference_id')->nullable();
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->foreignId('pushed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->index(['exam_definition_id', 'target_module', 'created_at']);
            $table->index(['exam_result_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_gradebook_export_logs');
    }
};
