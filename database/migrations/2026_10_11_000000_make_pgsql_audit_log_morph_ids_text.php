<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql' || ! Schema::hasTable('audit_logs')) {
            return;
        }

        $column = DB::selectOne(
            "SELECT data_type FROM information_schema.columns
             WHERE table_schema = current_schema()
             AND table_name = 'audit_logs'
             AND column_name = 'auditable_id'"
        );

        if ($column !== null && ! in_array($column->data_type, ['character varying', 'text'], true)) {
            DB::statement('ALTER TABLE "audit_logs" ALTER COLUMN "auditable_id" TYPE varchar(255) USING "auditable_id"::text');
        }
    }

    public function down(): void {}
};
