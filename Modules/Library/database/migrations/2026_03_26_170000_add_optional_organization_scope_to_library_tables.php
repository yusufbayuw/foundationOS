<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['members', 'book_copies', 'loans', 'fines'] as $table) {
            if (! Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->foreignId('organization_id')
                        ->nullable()
                        ->after('tenant_id')
                        ->constrained('organizations')
                        ->nullOnDelete();
                });
            }
        }

        // Relax existing organization scopes for centralized tenant-wide mode.
        try {
            Schema::table('books', function (Blueprint $blueprint): void {
                $blueprint->foreignId('organization_id')->nullable()->change();
            });
            Schema::table('book_categories', function (Blueprint $blueprint): void {
                $blueprint->foreignId('organization_id')->nullable()->change();
            });
        } catch (\Throwable) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE books MODIFY organization_id BIGINT UNSIGNED NULL');
                DB::statement('ALTER TABLE book_categories MODIFY organization_id BIGINT UNSIGNED NULL');
            }
        }
    }

    public function down(): void
    {
        try {
            Schema::table('books', function (Blueprint $blueprint): void {
                $blueprint->foreignId('organization_id')->nullable(false)->change();
            });
            Schema::table('book_categories', function (Blueprint $blueprint): void {
                $blueprint->foreignId('organization_id')->nullable(false)->change();
            });
        } catch (\Throwable) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE books MODIFY organization_id BIGINT UNSIGNED NOT NULL');
                DB::statement('ALTER TABLE book_categories MODIFY organization_id BIGINT UNSIGNED NOT NULL');
            }
        }

        foreach (['fines', 'loans', 'book_copies', 'members'] as $table) {
            if (Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->dropConstrainedForeignId('organization_id');
                });
            }
        }
    }
};
