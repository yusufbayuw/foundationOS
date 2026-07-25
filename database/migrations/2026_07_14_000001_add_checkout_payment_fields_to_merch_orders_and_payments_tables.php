<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merch_orders', function (Blueprint $table): void {
            $table->decimal('total_amount', 12, 2)->default(0)->after('description');
            $table->string('payment_reference')->nullable()->after('total_amount');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
            $table->timestamp('ready_for_pickup_at')->nullable()->after('paid_at');
            $table->unique('payment_reference');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->foreignId('merch_order_id')->nullable()->after('student_invoice_id')->constrained('merch_orders')->nullOnDelete();
            $table->string('payment_reference')->nullable()->after('reference_number');
            $table->json('gateway_payload')->nullable()->after('payment_reference');
            $table->unique('payment_reference');
            $table->foreignId('student_invoice_id')->nullable()->change();
            $table->foreignId('chart_of_account_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique(['payment_reference']);
            $table->dropConstrainedForeignId('merch_order_id');
            $table->dropColumn(['payment_reference', 'gateway_payload']);
            $table->foreignId('student_invoice_id')->nullable(false)->change();
            $table->foreignId('chart_of_account_id')->nullable(false)->change();
        });

        Schema::table('merch_orders', function (Blueprint $table): void {
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['total_amount', 'payment_reference', 'paid_at', 'ready_for_pickup_at']);
        });
    }
};
