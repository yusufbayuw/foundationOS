<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('procurement_categories')->nullOnDelete();
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('request_for_quotation_id')->references('id')->on('request_for_quotations')->nullOnDelete();
        });

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->nullOnDelete();
        });

        Schema::table('vendor_bills', function (Blueprint $table) {
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
        });

        Schema::table('purchase_requisition_items', function (Blueprint $table) {
            $table->foreign('budget_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisition_items', function (Blueprint $table) {
            $table->dropForeign(['budget_account_id']);
        });

        Schema::table('vendor_bills', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
        });

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['request_for_quotation_id']);
        });

        Schema::table('procurement_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['chart_of_account_id']);
        });
    }
};
