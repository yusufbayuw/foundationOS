<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('previous_contract_id')->nullable()->constrained('employment_contracts')->nullOnDelete();
            $table->string('contract_number');
            $table->string('contract_type')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('probation_period_months')->default(3);
            $table->decimal('basic_salary', 12, 2);
            $table->json('allowance_details')->nullable();
            $table->json('benefits')->nullable();
            $table->string('work_location')->nullable();
            $table->unsignedInteger('work_hours_per_week')->default(40);
            $table->text('termination_clause')->nullable();
            $table->string('status')->default('active');
            $table->boolean('signed_by_employee')->default(false);
            $table->boolean('signed_by_employer')->default(false);
            $table->string('document_file')->nullable();
            $table->unique(['tenant_id', 'contract_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_contracts');
    }
};
