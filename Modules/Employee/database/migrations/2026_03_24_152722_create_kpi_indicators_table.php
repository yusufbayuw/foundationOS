<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('measurement_unit')->nullable();
            $table->string('target_type')->nullable();
            $table->decimal('target_value', 8, 2)->nullable();
            $table->decimal('target_minimum', 8, 2)->nullable();
            $table->decimal('target_maximum', 8, 2)->nullable();
            $table->decimal('weight_percentage', 8, 2)->default(0);
            $table->string('scoring_method')->nullable();
            $table->text('formula')->nullable();
            $table->string('data_source')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['tenant_id', 'organization_id', 'code']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_indicators');
    }
};
