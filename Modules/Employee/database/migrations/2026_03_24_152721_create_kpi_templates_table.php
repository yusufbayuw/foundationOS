<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('name');
            $table->json('indicators');
            $table->decimal('total_weight', 8, 2)->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('kpi_scores')) {
            Schema::table('kpi_scores', function (Blueprint $table): void {
                $table->foreign('kpi_template_id')->references('id')->on('kpi_templates')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kpi_scores')) {
            Schema::table('kpi_scores', function (Blueprint $table): void {
                $table->dropForeign(['kpi_template_id']);
            });
        }

        Schema::dropIfExists('kpi_templates');
    }
};
