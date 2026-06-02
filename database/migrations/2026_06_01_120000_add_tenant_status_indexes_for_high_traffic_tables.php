<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('student_invoices', ['tenant_id', 'status'], 'student_invoices_tenant_status_idx');
        $this->addIndexIfMissing('applicants', ['tenant_id', 'status'], 'applicants_tenant_status_idx');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('student_invoices', 'student_invoices_tenant_status_idx');
        $this->dropIndexIfExists('applicants', 'applicants_tenant_status_idx');
    }

    /**
     * @param  list<string>  $columns
     */
    protected function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (Schema::hasIndex($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName): void {
            $blueprint->index($columns, $indexName);
        });
    }

    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasIndex($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName): void {
            $blueprint->dropIndex($indexName);
        });
    }
};
