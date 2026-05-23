<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_logs') && ! Schema::hasColumn('audit_logs', 'category')) {
            Schema::table('audit_logs', function (Blueprint $table): void {
                $table->string('category')->default('general')->after('action');
            });
        }

        if (Schema::hasTable('audit_logs') && ! Schema::hasIndex('audit_logs', 'audit_logs_tenant_category_created_idx')) {
            Schema::table('audit_logs', function (Blueprint $table): void {
                $table->index(['tenant_id', 'category', 'created_at'], 'audit_logs_tenant_category_created_idx');
            });
        }

        $this->ensureTenantCreatedIndex('journal_entry_lines', 'jel_tenant_created_idx');
        $this->ensureTenantCreatedIndex('attendance_logs', 'attendance_logs_tenant_created_idx');
        $this->ensureTenantCreatedIndex('moodle_sync_outbox', 'moodle_outbox_tenant_created_idx');
        $this->ensureTenantCreatedIndex('audit_logs', 'audit_logs_tenant_created_idx');
        $this->ensureTenantCreatedIndex('salary_slip_components', 'ssc_tenant_created_idx');
        $this->ensureTenantCreatedIndex('student_grades', 'student_grades_tenant_created_idx');
    }

    public function down(): void
    {
        if (Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'category')) {
            Schema::table('audit_logs', function (Blueprint $table): void {
                if (Schema::hasIndex('audit_logs', 'audit_logs_tenant_category_created_idx')) {
                    $table->dropIndex('audit_logs_tenant_category_created_idx');
                }
                $table->dropColumn('category');
            });
        }

        foreach ([
            'journal_entry_lines' => 'jel_tenant_created_idx',
            'attendance_logs' => 'attendance_logs_tenant_created_idx',
            'moodle_sync_outbox' => 'moodle_outbox_tenant_created_idx',
            'audit_logs' => 'audit_logs_tenant_created_idx',
            'salary_slip_components' => 'ssc_tenant_created_idx',
            'student_grades' => 'student_grades_tenant_created_idx',
        ] as $table => $index) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $index)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($index));
            }
        }
    }

    protected function ensureTenantCreatedIndex(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName): void {
            $blueprint->index(['tenant_id', 'created_at'], $indexName);
        });
    }
};
