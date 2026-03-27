<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Domain tables that map to Eloquent models in modules.
     *
     * @var list<string>
     */
    private array $tables = [
        'academic_periods',
        'academic_years',
        'achievement_types',
        'admission_periods',
        'applicants',
        'assessment_items',
        'assessments',
        'attendance_logs',
        'attendances',
        'audit_logs',
        'book_categories',
        'book_copies',
        'books',
        'budgets',
        'chart_of_accounts',
        'cities',
        'class_students',
        'classes',
        'collage_students',
        'countries',
        'course_offerings',
        'courses',
        'curricula',
        'departments',
        'districts',
        'employees',
        'employment_contracts',
        'exam_results',
        'exam_schedules',
        'faculties',
        'feeder_logs',
        'file_uploads',
        'fines',
        'goods_receipt_items',
        'goods_receipts',
        'journal_entries',
        'journal_entry_lines',
        'kpi_indicators',
        'kpi_scores',
        'kpi_templates',
        'leave_requests',
        'lecturers',
        'loans',
        'members',
        'modules',
        'organization_settings',
        'organizations',
        'payments',
        'payroll_components',
        'positions',
        'procurement_categories',
        'procurement_items',
        'provinces',
        'purchase_order_items',
        'purchase_orders',
        'purchase_requisition_items',
        'purchase_requisitions',
        'registrations',
        'request_for_quotations',
        'rfq_items',
        'rfq_vendors',
        'salary_slip_components',
        'salary_slips',
        'schedules',
        'shifts',
        'student_achievements',
        'student_assessment_answers',
        'student_grades',
        'student_invoice_items',
        'student_invoices',
        'students',
        'study_plan_items',
        'study_plans',
        'study_programs',
        'study_results',
        'subjects',
        'subscription_logs',
        'subscription_plans',
        'teachers',
        'tenant_modules',
        'tenant_roles',
        'tenant_settings',
        'tenants',
        'theses',
        'timezones',
        'tuition_types',
        'user_tenant_roles',
        'users',
        'vendor_bill_items',
        'vendor_bills',
        'vendors',
        'villages',
        'violation_types',
        'violations',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropSoftDeletes();
            });
        }
    }
};

