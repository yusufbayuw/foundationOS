<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Augment `course_prerequisites` (created by 2026_05_21_140100) with optional
 * fields used by the per-tenant prerequisite hint generator.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_prerequisites', function (Blueprint $table): void {
            if (! Schema::hasColumn('course_prerequisites', 'is_required')) {
                $table->boolean('is_required')->default(true)->after('is_strict');
            }
            if (! Schema::hasColumn('course_prerequisites', 'note')) {
                $table->string('note')->nullable()->after('is_required');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_prerequisites', function (Blueprint $table): void {
            if (Schema::hasColumn('course_prerequisites', 'note')) {
                $table->dropColumn('note');
            }
            if (Schema::hasColumn('course_prerequisites', 'is_required')) {
                $table->dropColumn('is_required');
            }
        });
    }
};
