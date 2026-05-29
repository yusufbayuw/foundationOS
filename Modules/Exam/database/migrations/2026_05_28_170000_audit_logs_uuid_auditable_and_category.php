<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs') || ! Schema::hasColumn('audit_logs', 'auditable_id')) {
            return;
        }

        $columnType = Schema::getColumnType('audit_logs', 'auditable_id');

        if (in_array($columnType, ['uuid', 'string', 'guid'], true)) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropMorphs('auditable');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->nullableUuidMorphs('auditable');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs') || ! Schema::hasColumn('audit_logs', 'auditable_id')) {
            return;
        }

        $columnType = Schema::getColumnType('audit_logs', 'auditable_id');

        if (! in_array($columnType, ['uuid', 'string', 'guid'], true)) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropMorphs('auditable');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->nullableMorphs('auditable');
        });
    }
};
