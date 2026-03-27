<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->index(['workflow_id', 'trigger_event', 'is_active']);
            $table->index(['step_id', 'trigger_event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_automated_actions');
    }
};
