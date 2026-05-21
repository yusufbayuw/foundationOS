<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_step_branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances')->cascadeOnDelete();
            $table->foreignId('split_step_id')->constrained('workflow_steps');
            $table->foreignId('branch_step_id')->constrained('workflow_steps');
            $table->foreignId('join_step_id')->nullable()->constrained('workflow_steps');
            $table->string('status', 32)->default('active');
            $table->string('outcome', 64)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['workflow_instance_id', 'status'], 'wf_branch_instance_status_idx');
            $table->index(['workflow_instance_id', 'split_step_id'], 'wf_branch_instance_split_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_step_branches');
    }
};
