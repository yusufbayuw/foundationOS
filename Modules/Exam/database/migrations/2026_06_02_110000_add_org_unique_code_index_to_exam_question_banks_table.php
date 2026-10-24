<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The original tenant/code unique key may also support the tenant FK.
        // Create its tenant-leading replacement before removing the old key.
        if (! Schema::hasIndex('exam_question_banks', 'exam_question_banks_tenant_org_code_unique')) {
            Schema::table('exam_question_banks', function (Blueprint $table): void {
                $table->unique(['tenant_id', 'organization_id', 'code'], 'exam_question_banks_tenant_org_code_unique');
            });
        }

        if (Schema::hasIndex('exam_question_banks', 'exam_question_banks_tenant_id_code_unique')) {
            Schema::table('exam_question_banks', function (Blueprint $table): void {
                $table->dropUnique('exam_question_banks_tenant_id_code_unique');
            });
        }
    }

    public function down(): void
    {
        // Restore the FK-supporting tenant index before dropping its replacement.
        if (! Schema::hasIndex('exam_question_banks', 'exam_question_banks_tenant_id_code_unique')) {
            Schema::table('exam_question_banks', function (Blueprint $table): void {
                $table->unique(['tenant_id', 'code']);
            });
        }

        if (Schema::hasIndex('exam_question_banks', 'exam_question_banks_tenant_org_code_unique')) {
            Schema::table('exam_question_banks', function (Blueprint $table): void {
                $table->dropUnique('exam_question_banks_tenant_org_code_unique');
            });
        }
    }
};
