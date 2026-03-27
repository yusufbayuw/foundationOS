<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instance_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances');
            $table->foreignId('step_id')->nullable()->constrained('workflow_steps')->nullOnDelete();
            $table->foreignId('transition_id')->nullable()->constrained('workflow_transitions')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('log_type');
            $table->string('action_taken')->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->json('payload_before')->nullable();
            $table->json('payload_after')->nullable();
            $table->json('form_data_snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index(['workflow_instance_id', 'logged_at']);
            $table->index(['actor_id', 'logged_at']);
            $table->index(['log_type', 'action_taken']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instance_logs');
    }
};
