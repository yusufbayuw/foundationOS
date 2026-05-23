<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_evidences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_instance_id')
                ->constrained('workflow_instances')
                ->cascadeOnDelete();
            $table->foreignId('workflow_step_id')
                ->constrained('workflow_steps')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('uploaded_by')->index();
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('label')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workflow_instance_id', 'workflow_step_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_evidences');
    }
};
