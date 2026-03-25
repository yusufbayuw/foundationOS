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
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreignId('preferred_vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->unsignedBigInteger('chart_of_account_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit_of_measure')->nullable();
            $table->decimal('estimated_price', 18, 2)->nullable();
            $table->decimal('last_purchase_price', 18, 2)->nullable();
            $table->text('specifications')->nullable();
            $table->unsignedInteger('minimum_order_quantity')->default(1);
            $table->unsignedInteger('lead_time_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'category_id']);
            $table->index('chart_of_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};
