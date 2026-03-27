<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->index(['assigned_to_type', 'assigned_to_id', 'status']);
            $table->index(['workflow_instance_id', 'step_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_assignments');
    }
};
