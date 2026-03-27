<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('workflow_id')->constrained('workflows');
            $table->unsignedInteger('workflow_version');
            $table->json('workflow_snapshot');
            $table->foreignId('current_step_id')->nullable()->constrained('workflow_steps')->nullOnDelete();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label')->nullable();
            $table->json('context_data')->nullable();
            $table->json('form_data')->nullable();
            $table->json('computed_data')->nullable();
            $table->string('status')->default('running');
            $table->json('current_assignees')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'organization_id', 'status']);
            $table->index(['workflow_id', 'workflow_version']);
            $table->index(['requester_id', 'status']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['current_step_id', 'due_at']);
            $table->index(['started_at', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instances');
    }
};
