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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('nrg')->nullable();
            $table->string('status')->nullable();
            $table->string('employment_status')->nullable();
            $table->date('join_date')->nullable();
            $table->date('resignation_date')->nullable();
            $table->boolean('is_certified')->default(false);
            $table->string('certification_year')->nullable();
            $table->string('certification_number')->nullable();
            $table->string('highest_education')->nullable();
            $table->string('major_study')->nullable();
            $table->string('university')->nullable();
            $table->string('functional_position')->nullable();
            $table->string('structural_position')->nullable();
            $table->json('subject_specializations')->nullable();
            $table->text('class_advisor_history')->nullable();
            $table->unsignedInteger('teaching_hours_per_week')->nullable();
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->decimal('allowance', 12, 2)->nullable();
            $table->string('bpjs_tk_number')->nullable();
            $table->string('bpjs_kes_number')->nullable();
            $table->unique(['tenant_id', 'nip']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
