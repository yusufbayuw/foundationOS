<?php

use App\Enums\ShopOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['marketplace_orders', 'merch_orders'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn($tableName, 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('status');
                }
            });

            DB::table($tableName)
                ->where('status', 'active')
                ->update(['status' => ShopOrderStatus::Paid->value]);
        }
    }

    public function down(): void
    {
        foreach (['marketplace_orders', 'merch_orders'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'rejection_reason')) {
                    $table->dropColumn('rejection_reason');
                }

                if (Schema::hasColumn($tableName, 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
            });
        }
    }
};
