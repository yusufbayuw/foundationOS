<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('audit_logs', 'prev_hash')) {
                $table->string('prev_hash', 64)->nullable()->after('category');
            }

            if (! Schema::hasColumn('audit_logs', 'current_hash')) {
                $table->string('current_hash', 64)->nullable()->after('prev_hash');
            }
        });

        Schema::table('risks', function (Blueprint $table): void {
            if (! Schema::hasColumn('risks', 'risk_category_id')) {
                $table->foreignId('risk_category_id')->nullable()->after('organization_id')->constrained('risk_categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('risks', 'riskable_type')) {
                $table->nullableMorphs('riskable');
            }

            if (! Schema::hasColumn('risks', 'score')) {
                $table->unsignedTinyInteger('likelihood')->default(1)->after('status');
                $table->unsignedTinyInteger('impact')->default(1)->after('likelihood');
                $table->unsignedSmallInteger('score')->default(1)->after('impact');
                $table->unsignedTinyInteger('residual_likelihood')->nullable()->after('score');
                $table->unsignedTinyInteger('residual_impact')->nullable()->after('residual_likelihood');
                $table->unsignedSmallInteger('residual_score')->nullable()->after('residual_impact');
            }
        });

        Schema::table('kpi_targets', function (Blueprint $table): void {
            if (! Schema::hasColumn('kpi_targets', 'kpi_metric_id')) {
                $table->foreignId('kpi_metric_id')->nullable()->after('organization_id')->constrained('kpi_metrics')->cascadeOnDelete();
                $table->string('period')->nullable()->after('kpi_metric_id');
                $table->decimal('target_value', 14, 4)->default(0)->after('period');
                $table->decimal('weight', 8, 4)->default(100)->after('target_value');
            }
        });

        Schema::table('kpi_actuals', function (Blueprint $table): void {
            if (! Schema::hasColumn('kpi_actuals', 'kpi_metric_id')) {
                $table->foreignId('kpi_metric_id')->nullable()->after('organization_id')->constrained('kpi_metrics')->cascadeOnDelete();
                $table->string('period')->nullable()->after('kpi_metric_id');
                $table->decimal('actual_value', 14, 4)->default(0)->after('period');
                $table->decimal('score', 8, 4)->nullable()->after('actual_value');
            }
        });

        Schema::table('kpi_cascades', function (Blueprint $table): void {
            if (! Schema::hasColumn('kpi_cascades', 'parent_kpi_metric_id')) {
                $table->foreignId('parent_kpi_metric_id')->nullable()->after('organization_id')->constrained('kpi_metrics')->cascadeOnDelete();
                $table->foreignId('child_kpi_metric_id')->nullable()->after('parent_kpi_metric_id')->constrained('kpi_metrics')->cascadeOnDelete();
            }
        });

        Schema::create('school_health_indices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('period');
            $table->decimal('score', 8, 2);
            $table->string('band');
            $table->json('signals')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'organization_id', 'period'], 'school_health_indices_scope_unique');
        });

        Schema::create('ai_call_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('feature');
            $table->string('provider');
            $table->json('input_payload')->nullable();
            $table->json('output_payload')->nullable();
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->decimal('cost_amount', 12, 6)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_call_logs');
        Schema::dropIfExists('school_health_indices');
    }
};
