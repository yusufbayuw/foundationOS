<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sellers')) {
            Schema::table('sellers', function (Blueprint $table): void {
                if (! Schema::hasColumn('sellers', 'seller_type')) {
                    $table->string('seller_type')->nullable()->after('meta');
                }
                if (! Schema::hasColumn('sellers', 'seller_id')) {
                    $table->unsignedBigInteger('seller_id')->nullable()->after('seller_type');
                }
                if (! Schema::hasColumn('sellers', 'verification_status')) {
                    $table->string('verification_status')->default('pending')->after('seller_id');
                }
            });
        }

        if (Schema::hasTable('marketplace_orders') && ! Schema::hasColumn('marketplace_orders', 'seller_id')) {
            Schema::table('marketplace_orders', function (Blueprint $table): void {
                $table->foreignId('seller_id')->nullable()->after('tenant_id')->constrained('sellers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('marketplace_orders') && Schema::hasColumn('marketplace_orders', 'seller_id')) {
            Schema::table('marketplace_orders', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('seller_id');
            });
        }
    }
};
