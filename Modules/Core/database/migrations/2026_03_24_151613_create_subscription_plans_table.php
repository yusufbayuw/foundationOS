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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->unsignedInteger('max_users')->default(1);
            $table->unsignedInteger('max_organizations')->default(1);
            $table->unsignedInteger('max_storage_gb')->default(1);
            $table->json('included_modules');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_recommended')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->foreign('subscription_plan_id')
                ->references('id')
                ->on('subscription_plans')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
        });

        Schema::dropIfExists('subscription_plans');
    }
};
