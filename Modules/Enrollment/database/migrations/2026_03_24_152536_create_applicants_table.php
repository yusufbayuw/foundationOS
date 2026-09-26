<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('admission_period_id')->constrained('admission_periods')->cascadeOnDelete();
            $table->string('registration_number');
            $table->string('full_name');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('previous_school')->nullable();
            $table->text('previous_school_address')->nullable();
            $table->string('nisn')->nullable();
            $table->string('ijazah_number')->nullable();
            $table->decimal('average_score', 8, 2)->nullable();
            $table->unsignedInteger('achievement_count')->default(0);
            $table->json('achievement_details')->nullable();
            $table->foreignId('program_choice_1_id')->nullable();
            $table->foreignId('program_choice_2_id')->nullable();
            $table->string('status')->default('registered');
            $table->decimal('test_score', 8, 2)->nullable();
            $table->decimal('interview_score', 8, 2)->nullable();
            $table->decimal('final_score', 8, 2)->nullable();
            $table->unsignedInteger('ranking')->nullable();
            $table->boolean('is_passed')->nullable();
            $table->foreignId('accepted_program_id')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->foreignId('converted_to_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('photo')->nullable();
            $table->json('documents')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['tenant_id', 'registration_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
