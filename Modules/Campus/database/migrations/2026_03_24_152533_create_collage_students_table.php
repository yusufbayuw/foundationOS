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
        Schema::create('collage_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('study_program_id')->nullable();
            $table->unsignedBigInteger('academic_advisor_id')->nullable();
            $table->string('student_number');
            $table->string('national_student_number')->nullable();
            $table->string('full_name')->nullable();
            $table->unsignedInteger('entry_year')->nullable();
            $table->unsignedInteger('entry_semester')->nullable();
            $table->string('admission_type')->nullable();
            $table->unsignedInteger('current_semester')->default(1);
            $table->string('status')->default('active');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('graduation_date')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'student_number']);
            $table->index(['study_program_id', 'academic_advisor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collage_students');
    }
};
