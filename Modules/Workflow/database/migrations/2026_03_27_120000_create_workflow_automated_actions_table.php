<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL can retain a table if a later index ALTER statement fails.
        if (! Schema::hasTable('workflow_automated_actions')) {
            Schema::create('workflow_automated_actions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
                $table->foreignId('step_id')->nullable()->constrained('workflow_steps')->nullOnDelete();
                $table->string('trigger_event');
                $table->string('action_type');
                $table->string('name')->nullable();
                $table->json('config')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        foreach (['id', 'workflow_id', 'step_id', 'trigger_event', 'action_type', 'name', 'config', 'is_active', 'sort_order', 'created_at', 'updated_at', 'deleted_at'] as $column) {
            if (! Schema::hasColumn('workflow_automated_actions', $column)) {
                throw new \RuntimeException('Existing workflow_automated_actions table is missing column '.$column.'; manual schema review required.');
            }
        }

        if (! Schema::hasIndex('workflow_automated_actions', 'wf_auto_trigger_active_idx')) {
            Schema::table('workflow_automated_actions', function (Blueprint $table): void {
                $table->index(['workflow_id', 'trigger_event', 'is_active'], 'wf_auto_trigger_active_idx');
            });
        }

        if (! Schema::hasIndex('workflow_automated_actions', 'workflow_automated_actions_step_id_trigger_event_index')) {
            Schema::table('workflow_automated_actions', function (Blueprint $table): void {
                $table->index(['step_id', 'trigger_event']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_automated_actions');
    }
};
