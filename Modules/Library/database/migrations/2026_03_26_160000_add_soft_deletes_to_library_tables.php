<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['book_categories', 'books', 'book_copies', 'members', 'loans', 'fines'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                if (! Schema::hasColumn($table, 'deleted_at')) {
                    $blueprint->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['book_categories', 'books', 'book_copies', 'members', 'loans', 'fines'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                if (Schema::hasColumn($table, 'deleted_at')) {
                    $blueprint->dropSoftDeletes();
                }
            });
        }
    }
};
