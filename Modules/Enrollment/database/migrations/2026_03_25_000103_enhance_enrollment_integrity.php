<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->foreignId('exam_schedule_id')
                ->nullable()
                ->after('applicant_id')
                ->constrained('exam_schedules')
                ->nullOnDelete();

            $table->unique(['applicant_id', 'exam_schedule_id'], 'exam_results_applicant_schedule_unique');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->unique(['applicant_id'], 'registrations_applicant_unique');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique('registrations_applicant_unique');
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropUnique('exam_results_applicant_schedule_unique');
            $table->dropConstrainedForeignId('exam_schedule_id');
        });
    }
};
