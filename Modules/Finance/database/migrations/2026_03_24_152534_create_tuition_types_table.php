<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('education_level')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('frequency')->nullable();
            $table->unsignedInteger('due_day')->default(10);
            $table->unsignedInteger('grace_period_days')->default(7);
            $table->decimal('late_fee_percentage', 8, 2)->default(0);
            $table->decimal('late_fee_fixed', 10, 2)->default(0);
            $table->boolean('discount_eligible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->unique(['tenant_id', 'organization_id', 'code']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_types');
    }
};
