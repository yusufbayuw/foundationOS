<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->decimal('price_per_seat', 10, 2)->default(0)->after('price_yearly');
            $table->decimal('price_per_module', 10, 2)->default(0)->after('price_per_seat');
            $table->unsignedInteger('free_seats')->default(0)->after('price_per_module');
            $table->unsignedInteger('free_modules')->default(0)->after('free_seats');
            $table->unsignedInteger('grace_period_days')->default(7)->after('free_modules');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['price_per_seat', 'price_per_module', 'free_seats', 'free_modules', 'grace_period_days']);
        });
    }
};
