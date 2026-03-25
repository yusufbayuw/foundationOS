<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_slip_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('salary_slip_id')->constrained('salary_slips')->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->nullable()->constrained('payroll_components')->nullOnDelete();
            $table->string('component_type')->nullable();
            $table->string('component_name');
            $table->string('calculation_type')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('percentage', 8, 2)->nullable();
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->text('formula_used')->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_mandatory')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_slip_components');
    }
};
