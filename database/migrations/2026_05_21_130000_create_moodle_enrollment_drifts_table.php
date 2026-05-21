<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moodle_enrollment_drifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('course_moodle_id')->nullable();
            $table->string('course_moodle_idnumber', 191)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('user_moodle_id')->nullable();
            $table->string('user_moodle_idnumber', 191)->nullable();
            $table->string('drift_type', 32);
            $table->string('expected_role', 64)->nullable();
            $table->string('actual_role', 64)->nullable();
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution', 64)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'drift_type'], 'moodle_drift_tenant_type_idx');
            $table->index(['tenant_id', 'class_id'], 'moodle_drift_tenant_class_idx');
            $table->index(['tenant_id', 'resolved_at'], 'moodle_drift_tenant_unresolved_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moodle_enrollment_drifts');
    }
};
