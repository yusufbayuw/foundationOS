<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('max_books')->default(3);
            $table->unsignedInteger('loan_period_days')->default(7);
            $table->decimal('fine_per_day', 10, 2)->default(1000);
            $table->unsignedInteger('max_extensions')->default(2);
            $table->unsignedInteger('grace_period_days')->default(0);
            $table->unsignedInteger('reservation_pickup_days')->default(2);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['tenant_id', 'organization_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_policies');
    }
};
