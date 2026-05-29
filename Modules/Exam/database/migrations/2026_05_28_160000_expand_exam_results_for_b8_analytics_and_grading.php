<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_definition_results', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('score');
            $table->unsignedInteger('suspicious_activity_count')->default(0)->after('percentage');
            $table->timestamp('submitted_at')->nullable()->after('status');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->decimal('manual_score', 10, 2)->nullable()->after('score');
            $table->text('feedback')->nullable()->after('manual_score');
            $table->json('rubric_json')->nullable()->after('feedback');
            $table->foreignId('graded_by')->nullable()->after('rubric_json')->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable()->after('graded_by');
        });

        Schema::create('exam_export_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignId('exported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('export_type');
            $table->string('file_name')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->index(['exam_definition_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_export_logs');

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('graded_by');
            $table->dropColumn(['manual_score', 'feedback', 'rubric_json', 'graded_at']);
        });

        Schema::table('exam_definition_results', function (Blueprint $table) {
            $table->dropColumn(['percentage', 'suspicious_activity_count', 'submitted_at']);
        });
    }
};
