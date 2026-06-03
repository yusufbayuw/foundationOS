<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_question_banks', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'code']);
            $table->unique(
                ['tenant_id', 'organization_id', 'code'],
                'exam_question_banks_tenant_org_code_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('exam_question_banks', function (Blueprint $table) {
            $table->dropUnique('exam_question_banks_tenant_org_code_unique');
            $table->unique(['tenant_id', 'code']);
        });
    }
};
