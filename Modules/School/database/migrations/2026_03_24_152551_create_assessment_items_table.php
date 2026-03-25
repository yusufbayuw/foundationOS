<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->string('item_type')->default('multiple_choice');
            $table->unsignedInteger('question_number');
            $table->text('question_text');
            $table->string('question_attachment')->nullable();
            $table->json('answer_options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->decimal('max_score', 8, 2)->default(100);
            $table->decimal('weight', 8, 2)->default(1);
            $table->string('difficulty_level')->nullable();
            $table->string('cognitive_level')->nullable();
            $table->text('answer_key_rubric')->nullable();
            $table->unique(['assessment_id', 'question_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_items');
    }
};
