<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL can retain a table if a later index ALTER statement fails.
        if (! Schema::hasTable('exam_gradebook_export_logs')) {
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
            });
        }

        foreach (['id', 'tenant_id', 'exam_definition_id', 'exam_result_id', 'exam_participant_id', 'target_module', 'target_reference_type', 'target_reference_id', 'status', 'error_message', 'pushed_by', 'metadata_json', 'created_at', 'updated_at'] as $column) {
            if (! Schema::hasColumn('exam_gradebook_export_logs', $column)) {
                throw new RuntimeException('Existing exam_gradebook_export_logs table is missing column '.$column.'; manual schema review required.');
            }
        }

        if (! Schema::hasIndex('exam_gradebook_export_logs', 'egl_definition_target_created_idx')) {
            Schema::table('exam_gradebook_export_logs', function (Blueprint $table): void {
                $table->index(['exam_definition_id', 'target_module', 'created_at'], 'egl_definition_target_created_idx');
            });
        }

        if (! Schema::hasIndex('exam_gradebook_export_logs', 'exam_gradebook_export_logs_exam_result_id_status_index')) {
            Schema::table('exam_gradebook_export_logs', function (Blueprint $table): void {
                $table->index(['exam_result_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_gradebook_export_logs');
    }
};
