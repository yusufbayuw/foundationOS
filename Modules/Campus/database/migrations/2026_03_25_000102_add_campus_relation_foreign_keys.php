<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->foreign('head_of_program_id')->references('id')->on('lecturers')->nullOnDelete();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs')->nullOnDelete();
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs')->nullOnDelete();
        });

        Schema::table('collage_students', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs')->nullOnDelete();
            $table->foreign('academic_advisor_id')->references('id')->on('lecturers')->nullOnDelete();
        });

        Schema::table('study_results', function (Blueprint $table) {
            $table->foreign('study_plan_item_id')->references('id')->on('study_plan_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('study_results', function (Blueprint $table) {
            $table->dropForeign(['study_plan_item_id']);
        });

        Schema::table('collage_students', function (Blueprint $table) {
            $table->dropForeign(['study_program_id']);
            $table->dropForeign(['academic_advisor_id']);
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropForeign(['study_program_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['study_program_id']);
        });

        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropForeign(['head_of_program_id']);
        });
    }
};
