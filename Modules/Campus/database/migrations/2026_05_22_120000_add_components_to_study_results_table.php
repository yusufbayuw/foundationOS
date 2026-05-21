<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_results', function (Blueprint $table) {
            $table->json('components_breakdown')->nullable()->after('weight_score');
            $table->timestamp('moodle_pulled_at')->nullable()->after('published_at');
            $table->string('source')->nullable()->after('notes');
        });

        Schema::table('collage_students', function (Blueprint $table) {
            // Cached GPA; nullable so existing rows untouched.
            if (! Schema::hasColumn('collage_students', 'gpa_cached')) {
                $table->decimal('gpa_cached', 4, 2)->nullable()->after('student_number');
            }
            if (! Schema::hasColumn('collage_students', 'gpa_recalculated_at')) {
                $table->timestamp('gpa_recalculated_at')->nullable()->after('gpa_cached');
            }
        });
    }

    public function down(): void
    {
        Schema::table('study_results', function (Blueprint $table) {
            $table->dropColumn(['components_breakdown', 'moodle_pulled_at', 'source']);
        });

        Schema::table('collage_students', function (Blueprint $table) {
            if (Schema::hasColumn('collage_students', 'gpa_cached')) {
                $table->dropColumn('gpa_cached');
            }
            if (Schema::hasColumn('collage_students', 'gpa_recalculated_at')) {
                $table->dropColumn('gpa_recalculated_at');
            }
        });
    }
};
