<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows');
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('step_type');
            $table->string('assignee_type')->nullable();
            $table->string('assignee_value')->nullable();
            $table->json('assignee_config')->nullable();
            $table->json('form_schema')->nullable();
            $table->json('action_schema')->nullable();
            $table->unsignedInteger('sla_hours')->nullable();
            $table->boolean('allow_reassign')->default(false);
            $table->boolean('allow_delegate')->default(false);
            $table->boolean('is_initial')->default(false);
            $table->boolean('is_terminal')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['workflow_id', 'code']);
            $table->unique(['workflow_id', 'uuid']);
            $table->index(['workflow_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
