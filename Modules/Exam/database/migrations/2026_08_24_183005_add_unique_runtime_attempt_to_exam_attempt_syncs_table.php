<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_attempt_syncs', function (Blueprint $table) {
            $table->unique(
                ['exam_definition_id', 'runtime_attempt_id'],
                'exam_attempt_syncs_definition_runtime_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempt_syncs', function (Blueprint $table) {
            $table->dropUnique('exam_attempt_syncs_definition_runtime_unique');
        });
    }
};
