<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prerequisite_course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('min_grade', 4, 2)->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'prerequisite_course_id'], 'course_prerequisites_unique');
            $table->index(['tenant_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
