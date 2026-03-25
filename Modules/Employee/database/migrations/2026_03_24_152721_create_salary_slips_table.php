<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('period_month');
            $table->string('period_year');
            $table->string('period_label');
            $table->decimal('basic_salary', 12, 2);
            $table->json('earnings_details');
            $table->json('deductions_details');
            $table->decimal('total_earnings', 12, 2);
            $table->decimal('total_deductions', 12, 2);
            $table->decimal('net_salary', 12, 2);
            $table->json('tax_details')->nullable();
            $table->json('bpjs_details')->nullable();
            $table->unsignedInteger('working_days')->default(0);
            $table->decimal('working_hours', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->unsignedInteger('leave_days')->default(0);
            $table->unsignedInteger('absent_days')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->string('paid_via')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
