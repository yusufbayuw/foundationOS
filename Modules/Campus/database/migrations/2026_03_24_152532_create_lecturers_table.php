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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('study_program_id')->nullable();
            $table->string('nidn')->nullable();
            $table->string('employee_number')->nullable();
            $table->string('full_name')->nullable();
            $table->string('academic_title_prefix')->nullable();
            $table->string('academic_title_suffix')->nullable();
            $table->string('functional_position')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('join_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'nidn']);
            $table->index('study_program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
