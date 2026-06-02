<?php

namespace Tests\Feature;

use App\Services\CrossModuleReportService;
use App\Services\ExecutiveMetricsService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\TuitionType;
use Modules\Monitoring\Models\AuditLog;
use Modules\School\Models\Assessment;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Subject;
use Modules\School\Services\MonthlyTuitionInvoiceService;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

class RoadmapPhase3Test extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_executive_metrics_service_returns_expected_keys(): void
    {
        [$tenant, $org] = $this->makeTenantWithOrg();

        $coa = ChartOfAccount::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => '1101',
            'name' => 'Kas',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $invoice = StudentInvoice::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'invoiceable_type' => 'school_student',
            'invoiceable_id' => 1,
            'invoice_number' => 'INV-TEST-001',
            'invoice_type' => 'tuition',
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'amount' => 500_000,
            'total_amount' => 500_000,
            'paid_amount' => 0,
            'remaining_amount' => 500_000,
            'status' => 'issued',
        ]);

        Payment::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'student_invoice_id' => $invoice->id,
            'chart_of_account_id' => $coa->id,
            'payment_number' => 'PAY-001',
            'amount' => 500_000,
            'status' => 'verified',
            'payment_date' => now(),
        ]);

        $metrics = app(ExecutiveMetricsService::class)->forTenant((int) $tenant->id);

        $this->assertArrayHasKey('revenue_mtd', $metrics);
        $this->assertArrayHasKey('pending_approvals', $metrics);
        $this->assertEqualsWithDelta(500_000.0, $metrics['revenue_mtd'], 0.01);
    }

    public function test_cross_module_cost_efficiency_report(): void
    {
        $tenant = $this->makeTenant();
        $from = now()->startOfMonth();
        $to = now()->endOfMonth();

        $report = app(CrossModuleReportService::class)->costEfficiency((int) $tenant->id, $from, $to);

        $this->assertArrayHasKey('revenue', $report);
        $this->assertArrayHasKey('payroll', $report);
        $this->assertArrayHasKey('efficiency_ratio', $report);
    }

    public function test_failed_login_writes_security_audit_log(): void
    {
        event(new Failed('web', ['email' => 'nobody@example.com', 'password' => 'wrong'], []));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security.login_failed',
            'category' => AuditLog::CATEGORY_SECURITY,
            'status' => 'failed',
        ]);
    }

    public function test_monthly_tuition_generates_invoice_for_active_student(): void
    {
        [$tenant, $org] = $this->makeTenantWithOrg();
        $student = $this->makeActiveStudent($tenant, $org);

        TuitionType::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => 'SPP-BULAN',
            'name' => 'SPP Bulanan',
            'amount' => 1_500_000,
            'frequency' => 'monthly',
            'due_day' => 10,
            'is_active' => true,
        ]);

        $created = app(MonthlyTuitionInvoiceService::class)->generateForTenant((int) $tenant->id, now()->format('Y-m'));

        $this->assertSame(1, $created);
        $this->assertDatabaseHas('student_invoices', [
            'tenant_id' => $tenant->id,
            'invoiceable_type' => 'school_student',
            'invoiceable_id' => $student->id,
            'status' => 'issued',
        ]);
    }

    public function test_locked_student_grade_update_starts_revision_workflow(): void
    {
        [$tenant, $org, $homeroom] = $this->makeTenantWithUsers();
        $student = $this->makeActiveStudent($tenant, $org);
        $assessment = $this->makeAssessment($tenant, $org);
        $this->seedGradeRevisionWorkflow($tenant, $homeroom);

        $grade = StudentGrade::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'score' => 80,
            'final_score' => 80,
            'is_locked' => true,
        ]);

        $this->actingAs($homeroom);
        $grade->update(['score' => 85, 'notes' => 'Koreksi input']);

        $this->assertDatabaseHas('workflow_instances', [
            'tenant_id' => $tenant->id,
            'subject_type' => StudentGrade::class,
            'subject_id' => $grade->id,
        ]);
    }

    public function test_unlocked_student_grade_update_does_not_start_workflow(): void
    {
        [$tenant, $org, $homeroom] = $this->makeTenantWithUsers();
        $student = $this->makeActiveStudent($tenant, $org);
        $assessment = $this->makeAssessment($tenant, $org);

        $grade = StudentGrade::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'score' => 70,
            'is_locked' => false,
        ]);

        $this->actingAs($homeroom);
        $grade->update(['score' => 75]);

        $this->assertDatabaseMissing('workflow_instances', [
            'subject_type' => StudentGrade::class,
            'subject_id' => $grade->id,
        ]);
    }

    protected function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 't-'.Str::random(4),
            'name' => 'Test Tenant',
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    /**
     * @return array{0: Tenant, 1: Organization}
     */
    protected function makeTenantWithOrg(): array
    {
        $tenant = $this->makeTenant();
        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Campus',
        ]);

        return [$tenant, $org];
    }

    protected function makeActiveStudent(Tenant $tenant, Organization $org): Student
    {
        $user = User::create([
            'name' => 'Student One',
            'email' => 'student'.Str::random(5).'@test.com',
            'password' => 'password',
        ]);

        $student = Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'nis' => 'NIS'.random_int(1000, 9999),
            'status' => 'active',
        ]);

        $period = $this->makeAcademicPeriod($tenant, $org);

        $class = SchoolClass::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'academic_period_id' => $period->id,
            'name' => 'Class 10A',
            'code' => '10A',
            'grade_level' => '10',
            'is_active' => true,
        ]);

        ClassStudent::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);

        return $student;
    }

    protected function makeAcademicPeriod(Tenant $tenant, Organization $org): AcademicPeriod
    {
        $year = AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'name' => '2025/2026',
            'code' => '2025-26',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);

        return AcademicPeriod::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'code' => 'S1',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);
    }

    protected function makeAssessment(Tenant $tenant, Organization $org): Assessment
    {
        $subject = Subject::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'name' => 'Mathematics',
            'code' => 'MAT',
        ]);

        $class = SchoolClass::withoutTenantScope()->where('tenant_id', $tenant->id)->first();

        return Assessment::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'name' => 'UTS 1',
            'code' => 'UTS1',
            'type' => 'exam',
            'max_score' => 100,
        ]);
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User, 3: User}
     */
    /**
     * @return array{0: Tenant, 1: Organization, 2: User}
     */
    protected function makeTenantWithUsers(): array
    {
        [$tenant, $org] = $this->makeTenantWithOrg();
        $homeroom = User::create(['name' => 'Homeroom', 'email' => 'h'.Str::random(4).'@t.com', 'password' => 'password']);

        return [$tenant, $org, $homeroom];
    }

    protected function seedGradeRevisionWorkflow(Tenant $tenant, User $homeroom): void
    {
        $workflow = Workflow::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'student-grade-revision',
            'name' => 'Grade Revision',
            'module' => 'School',
            'subject_type' => StudentGrade::class,
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $homeroom->id,
        ]);

        $start = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'homeroom_review',
            'name' => 'Homeroom Review',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $homeroom->id,
            'action_schema' => [['name' => 'approve', 'label' => 'Approve']],
            'sort_order' => 1,
            'is_initial' => true,
        ]);

        $end = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'completed',
            'name' => 'Completed',
            'step_type' => 'end',
            'sort_order' => 2,
            'is_terminal' => true,
        ]);

        WorkflowTransition::query()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $end->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'condition_rules' => ['==' => [1, 1]],
            'is_default' => true,
        ]);
    }
}
