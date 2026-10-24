<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A failed ALTER INDEX on MySQL may leave the CREATE TABLE behind.
        if (! Schema::hasTable('workflow_assignments')) {
            Schema::create('workflow_assignments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('workflow_instance_id')->constrained('workflow_instances');
                $table->foreignId('step_id')->constrained('workflow_steps');
                $table->string('assigned_to_type')->default('user');
                $table->unsignedBigInteger('assigned_to_id');
                $table->string('assignment_role')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('assigned_at');
                $table->timestamp('claimed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('due_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        foreach (['id', 'workflow_instance_id', 'step_id', 'assigned_to_type', 'assigned_to_id', 'assignment_role', 'status', 'assigned_at', 'claimed_at', 'completed_at', 'due_at', 'meta', 'created_at', 'updated_at'] as $column) {
            if (! Schema::hasColumn('workflow_assignments', $column)) {
                throw new RuntimeException('Existing workflow_assignments table is missing column '.$column.'; manual schema review required.');
            }
        }

        if (! Schema::hasIndex('workflow_assignments', 'wf_assign_assignee_status_idx')) {
            Schema::table('workflow_assignments', function (Blueprint $table): void {
                $table->index(['assigned_to_type', 'assigned_to_id', 'status'], 'wf_assign_assignee_status_idx');
            });
        }

        if (! Schema::hasIndex('workflow_assignments', 'workflow_assignments_workflow_instance_id_step_id_status_index')) {
            Schema::table('workflow_assignments', function (Blueprint $table): void {
                $table->index(['workflow_instance_id', 'step_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_assignments');
    }
};
