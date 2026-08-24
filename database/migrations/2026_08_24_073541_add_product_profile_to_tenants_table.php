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
            $table->string('product_profile_code', 50)
                ->nullable()
                ->after('subscription_plan_id')
                ->index();
            $table->string('product_profile_version', 20)
                ->nullable()
                ->after('product_profile_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['product_profile_code']);
            $table->dropColumn(['product_profile_code', 'product_profile_version']);
        });
    }
};
