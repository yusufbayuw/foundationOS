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
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('grace_period_ends_at')->nullable()->after('subscription_expires_at');
            $table->string('midtrans_customer_id')->nullable()->after('grace_period_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['grace_period_ends_at', 'midtrans_customer_id']);
        });
    }
};
