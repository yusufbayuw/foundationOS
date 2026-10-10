<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql' || ! Schema::hasTable('notifications')) {
            return;
        }

        $column = DB::selectOne(
            "SELECT data_type FROM information_schema.columns
             WHERE table_schema = current_schema()
             AND table_name = 'notifications'
             AND column_name = 'data'"
        );

        if ($column !== null && in_array($column->data_type, ['text', 'json'], true)) {
            DB::statement('ALTER TABLE "notifications" ALTER COLUMN "data" TYPE jsonb USING "data"::jsonb');
        }
    }

    /**
     * Preserve JSONB on rollback: reverting to text breaks Filament notification queries.
     */
    public function down(): void
    {
    }
};
