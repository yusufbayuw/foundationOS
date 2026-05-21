<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('prerequisite_course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('min_grade', 5, 2)->nullable();
            $table->boolean('is_strict')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'prerequisite_course_id'], 'course_prereq_unique');
            $table->index(['tenant_id', 'course_id'], 'course_prereq_tenant_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
