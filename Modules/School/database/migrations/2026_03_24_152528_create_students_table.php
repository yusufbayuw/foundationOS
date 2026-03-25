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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('entry_type')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('previous_school_npsn')->nullable();
            $table->string('status')->default('active');
            $table->date('graduation_date')->nullable();
            $table->string('ijazah_number')->nullable();
            $table->string('skhun_number')->nullable();
            $table->string('track')->nullable();
            $table->json('extracurricular_activities')->nullable();
            $table->json('achievements')->nullable();
            $table->json('health_notes')->nullable();
            $table->json('special_needs')->nullable();
            $table->string('scholarship_status')->nullable();
            $table->string('family_card_number')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_nik')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_nik')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('residence_type')->nullable();
            $table->string('transport_type')->nullable();
            $table->unsignedInteger('travel_time_minutes')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->unique(['tenant_id', 'nis']);
            $table->unique(['tenant_id', 'nisn']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
