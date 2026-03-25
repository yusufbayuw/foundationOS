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
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collage_student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_lecturer_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->foreignId('examiner_lecturer_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->string('title');
            $table->string('research_area')->nullable();
            $table->timestamp('proposal_submitted_at')->nullable();
            $table->timestamp('defense_date')->nullable();
            $table->string('status')->default('proposal');
            $table->string('grade_letter')->nullable();
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
