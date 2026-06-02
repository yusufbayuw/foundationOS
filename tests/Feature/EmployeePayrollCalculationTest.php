<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\PayrollComponent;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;
use Modules\Employee\Services\PayrollCalculationService;
use Modules\Employee\Services\PayrollJournalService;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use RuntimeException;
use Tests\TestCase;

class EmployeePayrollCalculationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_payroll_calculation_creates_complete_salary_slip_from_components_and_attendance(): void
    {
        [$tenant, $organization, $employee] = $this->makeEmployee('payroll-calc');

        PayrollComponent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'TUNJ',
            'name' => 'Tunjangan Tetap',
            'type' => 'earning',
            'calculation_type' => 'fixed',
            'amount' => 500_000,
            'is_active' => true,
            'display_order' => 1,
        ]);

        PayrollComponent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'PPh21',
            'name' => 'PPh21',
            'type' => 'deduction',
            'calculation_type' => 'percentage',
            'percentage' => 5,
            'is_active' => true,
            'display_order' => 2,
        ]);

        PayrollComponent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'LEMBUR',
            'name' => 'Lembur',
            'type' => 'earning',
            'calculation_type' => 'formula',
            'formula' => '{overtime_hours} * 50000',
            'is_active' => true,
            'display_order' => 3,
        ]);

        AttendanceLog::query()->create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'date' => '2026-05-02',
            'work_hours' => 8,
            'overtime_hours' => 2,
            'status' => 'present',
        ]);

        AttendanceLog::query()->create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'date' => '2026-05-03',
            'work_hours' => 7,
            'overtime_hours' => 0,
            'status' => 'late',
        ]);

        AttendanceLog::query()->create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'date' => '2026-05-04',
            'work_hours' => 0,
            'overtime_hours' => 0,
            'status' => 'absent',
        ]);

        $slip = app(PayrollCalculationService::class)->calculate($employee, 5, 2026);

        $this->assertSame($employee->id, $slip->employee_id);
        $this->assertSame('May 2026', $slip->period_label);
        $this->assertEqualsWithDelta(5_600_000, (float) $slip->total_earnings, 0.01);
        $this->assertEqualsWithDelta(250_000, (float) $slip->total_deductions, 0.01);
        $this->assertEqualsWithDelta(5_350_000, (float) $slip->net_salary, 0.01);
        $this->assertSame(2, $slip->working_days);
        $this->assertSame(1, $slip->absent_days);
        $this->assertEqualsWithDelta(2, (float) $slip->overtime_hours, 0.01);
        $this->assertSame(3, SalarySlipComponent::query()->where('salary_slip_id', $slip->id)->count());
    }

    public function test_generate_payroll_dry_run_does_not_persist_salary_slips(): void
    {
        [$tenant, $organization] = $this->makeEmployee('payroll-dry-run');

        PayrollComponent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'TUNJ',
            'name' => 'Tunjangan Tetap',
            'type' => 'earning',
            'calculation_type' => 'fixed',
            'amount' => 500_000,
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->artisan('employee:generate-payroll', [
            '--tenant' => $tenant->id,
            '--month' => 5,
            '--year' => 2026,
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertSame(0, SalarySlip::query()->count());
        $this->assertSame(0, SalarySlipComponent::query()->count());
    }

    public function test_generate_payroll_requires_tenant_option(): void
    {
        $this->artisan('employee:generate-payroll', [
            '--month' => 5,
            '--year' => 2026,
        ])->assertFailed();
    }

    public function test_paid_salary_slip_posts_balanced_payroll_journal(): void
    {
        [$tenant, $organization, $employee] = $this->makeEmployee('payroll-journal');

        $salaryExpense = $this->makeAccount($tenant, $organization, '5101', 'Beban Gaji', 'expense');
        $cash = $this->makeAccount($tenant, $organization, '1101', 'Kas Operasional', 'asset');
        $taxPayable = $this->makeAccount($tenant, $organization, '2101', 'Hutang PPh21', 'liability');

        PayrollComponent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'TUNJ',
            'name' => 'Tunjangan Tetap',
            'type' => 'earning',
            'calculation_type' => 'fixed',
            'amount' => 500_000,
            'is_active' => true,
            'display_order' => 1,
        ]);

        PayrollComponent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'PPh21',
            'name' => 'PPh21',
            'type' => 'deduction',
            'calculation_type' => 'percentage',
            'percentage' => 5,
            'is_active' => true,
            'display_order' => 2,
            'chart_of_account_id' => $taxPayable->id,
        ]);

        $slip = app(PayrollCalculationService::class)->calculate($employee, 5, 2026);
        $slip->update([
            'status' => 'paid',
            'paid_at' => '2026-05-31 10:00:00',
            'paid_via' => 'bank',
        ]);

        $journal = app(PayrollJournalService::class)->postForSlip($slip->fresh());

        $this->assertTrue((bool) $journal->is_balanced);
        $this->assertFalse((bool) $journal->is_posted);
        $this->assertEqualsWithDelta(5_500_000, (float) $journal->total_debit, 0.01);
        $this->assertEqualsWithDelta(5_500_000, (float) $journal->total_credit, 0.01);
        $this->assertSame($journal->id, $slip->fresh()->journal_entry_id);

        $this->assertDatabaseHas(JournalEntryLine::class, [
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $salaryExpense->id,
            'debit' => 5_500_000,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas(JournalEntryLine::class, [
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $taxPayable->id,
            'debit' => 0,
            'credit' => 250_000,
        ]);

        $this->assertDatabaseHas(JournalEntryLine::class, [
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $cash->id,
            'debit' => 0,
            'credit' => 5_250_000,
        ]);
    }

    public function test_payroll_journal_requires_salary_expense_and_cash_accounts(): void
    {
        [, , $employee] = $this->makeEmployee('payroll-journal-missing-coa');

        $slip = app(PayrollCalculationService::class)->calculate($employee, 5, 2026);
        $slip->update([
            'status' => 'paid',
            'paid_at' => '2026-05-31 10:00:00',
            'paid_via' => 'bank',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payroll journal requires an active salary expense account');

        try {
            app(PayrollJournalService::class)->postForSlip($slip->fresh());
        } finally {
            $this->assertSame(0, JournalEntry::query()->count());
            $this->assertSame(0, JournalEntryLine::query()->count());
        }
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: Employee}
     */
    private function makeEmployee(string $code): array
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => Str::title(str_replace('-', ' ', $code)),
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => "{$code}-org",
            'name' => "{$code} Organization",
        ]);

        $user = User::factory()->create();

        $employee = Employee::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'employee_number' => strtoupper($code),
            'full_name' => 'Payroll Employee',
            'email' => "{$code}@example.test",
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'join_date' => '2026-01-01',
            'basic_salary' => 5_000_000,
        ]);

        return [$tenant, $organization, $employee];
    }

    private function makeAccount(
        Tenant $tenant,
        Organization $organization,
        string $code,
        string $name,
        string $type,
    ): ChartOfAccount {
        return ChartOfAccount::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => $code,
            'name' => $name,
            'level' => 1,
            'type' => $type,
            'normal_balance' => $type === 'asset' || $type === 'expense' ? 'debit' : 'credit',
            'is_active' => true,
        ]);
    }
}
