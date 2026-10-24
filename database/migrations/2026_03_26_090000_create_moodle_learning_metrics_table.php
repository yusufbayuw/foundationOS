<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL may leave the table behind when a later ALTER TABLE index fails.
        // Re-run safely without dropping existing records or recreating the table.
        if (! Schema::hasTable('moodle_learning_metrics')) {
            Schema::create('moodle_learning_metrics', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('fos_user_id')->nullable();
                $table->unsignedBigInteger('fos_course_id')->nullable();
                $table->unsignedBigInteger('moodle_user_id')->nullable();
                $table->unsignedBigInteger('moodle_course_id')->nullable();
                $table->string('metric_type', 32);
                $table->json('payload');
                $table->timestamp('pulled_at')->nullable();
                $table->timestamps();
            });
        }

        foreach (['id', 'tenant_id', 'fos_user_id', 'fos_course_id', 'moodle_user_id', 'moodle_course_id', 'metric_type', 'payload', 'pulled_at', 'created_at', 'updated_at'] as $column) {
            if (! Schema::hasColumn('moodle_learning_metrics', $column)) {
                throw new RuntimeException('Existing moodle_learning_metrics table is missing column '.$column.'; manual schema review required.');
            }
        }

        if (! Schema::hasIndex('moodle_learning_metrics', 'moodle_learning_metrics_tenant_id_metric_type_index')) {
            Schema::table('moodle_learning_metrics', function (Blueprint $table): void {
                $table->index(['tenant_id', 'metric_type']);
            });
        }

        if (! Schema::hasIndex('moodle_learning_metrics', 'mlm_fos_user_course_metric_idx')) {
            Schema::table('moodle_learning_metrics', function (Blueprint $table): void {
                $table->index(['fos_user_id', 'fos_course_id', 'metric_type'], 'mlm_fos_user_course_metric_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('moodle_learning_metrics');
    }
};
