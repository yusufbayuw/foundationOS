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
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('procurement_item_id')->nullable()->constrained('procurement_items')->nullOnDelete();
            $table->foreignId('preferred_vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->unsignedInteger('quantity_requested')->default(1);
            $table->string('unit_of_measure')->nullable();
            $table->decimal('estimated_unit_price', 18, 2)->default(0);
            $table->decimal('estimated_total_price', 18, 2)->default(0);
            $table->date('required_date')->nullable();
            $table->text('usage_purpose')->nullable();
            $table->unsignedBigInteger('budget_account_id')->nullable();
            $table->string('status')->default('requested');
            $table->unsignedInteger('ordered_quantity')->default(0);
            $table->unsignedInteger('received_quantity')->default(0);
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('budget_account_id');
        });

        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table): void {
                $table->foreign('purchase_requisition_item_id')
                    ->references('id')
                    ->on('purchase_requisition_items')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table): void {
                $table->dropForeign(['purchase_requisition_item_id']);
            });
        }

        Schema::dropIfExists('purchase_requisition_items');
    }
};
