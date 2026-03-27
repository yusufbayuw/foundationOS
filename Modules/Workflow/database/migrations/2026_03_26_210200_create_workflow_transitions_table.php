<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_transitions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows');
            $table->foreignId('from_step_id')->constrained('workflow_steps');
            $table->foreignId('to_step_id')->nullable()->constrained('workflow_steps')->nullOnDelete();
            $table->string('action_name');
            $table->string('rule_type')->default('json_logic');
            $table->json('condition_rules')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_default')->default(false);
            $table->json('transition_meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['from_step_id', 'action_name', 'priority']);
            $table->index(['workflow_id', 'from_step_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
