<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Services\FinanceControlService;
use Tests\TestCase;

class FinanceControlTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_verify_payment_updates_invoice_and_creates_posted_journal(): void
    {
        [$tenant, $organization, $actor] = $this->makeFinanceContext();
        [$cashAccount, $receivableAccount] = $this->makeAccounts($tenant, $organization);

        TenantSetting::query()->create([
            'tenant_id' => $tenant->id,
            'group' => 'finance',
            'key' => 'default_receivable_account_id',
            'value' => (string) $receivableAccount->id,
            'type' => 'integer',
        ]);

        $invoice = StudentInvoice::query()->create([
            'tenant_id' => $tenant->id,
            'invoiceable_type' => Organization::class,
            'invoiceable_id' => $organization->id,
            'invoice_number' => 'INV-'.strtoupper(Str::random(8)),
            'invoice_type' => 'student_invoice',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'amount' => 1000000,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'remaining_amount' => 1000000,
            'status' => 'issued',
            'description' => 'Student invoice for finance control test.',
        ]);

        $payment = Payment::query()->create([
            'tenant_id' => $tenant->id,
            'student_invoice_id' => $invoice->id,
            'chart_of_account_id' => $cashAccount->id,
            'payment_number' => 'PAY-'.strtoupper(Str::random(8)),
            'payment_date' => now()->toDateString(),
            'amount' => 1000000,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $verified = app(FinanceControlService::class)->verifyPayment($payment, $actor, 'Validated by finance controller.');

        $invoice->refresh();

        $this->assertSame('verified', $verified->status);
        $this->assertSame('verified', app(FinanceControlService::class)->verifyPayment($payment, $actor)->status);
        $this->assertSame('paid', $invoice->status);
        $this->assertSame('1000000.00', number_format((float) $invoice->paid_amount, 2, '.', ''));
        $this->assertSame('0.00', number_format((float) $invoice->remaining_amount, 2, '.', ''));

        $journal = JournalEntry::query()
            ->where('tenant_id', $tenant->id)
            ->where('entry_number', 'PAY-'.$payment->payment_number)
            ->first();

        $this->assertNotNull($journal);
        $this->assertTrue((bool) $journal->is_posted);
        $this->assertDatabaseCount('journal_entry_lines', 2);
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'auditable_type' => 'payment',
            'auditable_id' => $payment->id,
            'action' => 'finance_payment_verified',
        ]);
    }

    public function test_budget_approval_and_journal_post_reverse_are_locked_and_audited(): void
    {
        [$tenant, $organization, $actor] = $this->makeFinanceContext();
        [$cashAccount, $receivableAccount] = $this->makeAccounts($tenant, $organization);

        $budget = Budget::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'chart_of_account_id' => $receivableAccount->id,
            'fiscal_year' => '2026',
            'name' => 'Operations Budget',
            'code' => 'BGT-OPS-2026',
            'allocated_amount' => 50000000,
            'used_amount' => 0,
            'remaining_amount' => 50000000,
            'description' => 'Initial operating budget.',
            'status' => 'draft',
        ]);

        $approvedBudget = app(FinanceControlService::class)->approveBudget($budget, $actor, 'Budget approved for execution.');

        $this->assertSame('approved', $approvedBudget->status);
        $this->assertSame($actor->id, $approvedBudget->approved_by);
        $this->assertStringContainsString('Budget approved for execution.', (string) $approvedBudget->description);

        $journal = JournalEntry::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'entry_number' => 'JE-'.strtoupper(Str::random(8)),
            'date' => now()->toDateString(),
            'description' => 'Manual journal for testing.',
            'total_debit' => 0,
            'total_credit' => 0,
            'is_balanced' => false,
            'is_posted' => false,
            'is_reversed' => false,
        ]);

        $journal->lines()->createMany([
            [
                'tenant_id' => $tenant->id,
                'chart_of_account_id' => $cashAccount->id,
                'description' => 'Debit cash',
                'debit' => 250000,
                'credit' => 0,
            ],
            [
                'tenant_id' => $tenant->id,
                'chart_of_account_id' => $receivableAccount->id,
                'description' => 'Credit receivable',
                'debit' => 0,
                'credit' => 250000,
            ],
        ]);

        $postedJournal = app(FinanceControlService::class)->postJournalEntry($journal, $actor, 'Posted after review.');
        $reversedJournal = app(FinanceControlService::class)->reverseJournalEntry($postedJournal, $actor, 'Correction required.');

        $this->assertTrue((bool) $postedJournal->fresh()->is_posted);
        $this->assertTrue((bool) $reversedJournal->is_reversed);

        $reversal = JournalEntry::query()
            ->where('tenant_id', $tenant->id)
            ->where('reversed_entry_id', $journal->id)
            ->sole();

        $this->assertSame('REV-'.$journal->id, $reversal->entry_number);
        $this->assertTrue((bool) $reversal->is_posted);
        $this->assertTrue((bool) $reversal->is_balanced);
        $this->assertEqualsWithDelta(250000, (float) $reversal->total_debit, 0.01);
        $this->assertEqualsWithDelta(250000, (float) $reversal->total_credit, 0.01);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $reversal->id,
            'chart_of_account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => 250000,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $reversal->id,
            'chart_of_account_id' => $receivableAccount->id,
            'debit' => 250000,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'auditable_type' => 'budget',
            'auditable_id' => $budget->id,
            'action' => 'finance_budget_approved',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'auditable_type' => 'journal_entry',
            'auditable_id' => $journal->id,
            'action' => 'finance_journal_reversed',
        ]);
    }

    public function test_reversal_is_atomic_and_cannot_create_a_duplicate_journal(): void
    {
        [$tenant, $organization, $actor] = $this->makeFinanceContext();
        [$cashAccount, $receivableAccount] = $this->makeAccounts($tenant, $organization);

        $journal = JournalEntry::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'entry_number' => 'JE-REVERSAL-ONCE',
            'date' => now()->toDateString(),
            'description' => 'Reversal idempotency regression.',
            'total_debit' => 150000,
            'total_credit' => 150000,
            'is_balanced' => true,
            'is_posted' => true,
        ]);

        $journal->lines()->createMany([
            ['tenant_id' => $tenant->id, 'chart_of_account_id' => $cashAccount->id, 'debit' => 150000, 'credit' => 0],
            ['tenant_id' => $tenant->id, 'chart_of_account_id' => $receivableAccount->id, 'debit' => 0, 'credit' => 150000],
        ]);

        $service = app(FinanceControlService::class);
        $service->reverseJournalEntry($journal, $actor, 'Correct duplicate booking.');

        try {
            $service->reverseJournalEntry($journal, $actor, 'Repeated reversal attempt.');
            $this->fail('An already reversed journal must reject a second reversal.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Only posted and unreversed', $exception->getMessage());
        }

        $this->assertSame(1, JournalEntry::query()->where('reversed_entry_id', $journal->id)->count());
        $this->assertSame(2, JournalEntry::query()->where('tenant_id', $tenant->id)->count());
        $this->assertDatabaseCount('journal_entry_lines', 4);
        $this->assertSame('Correct duplicate booking.', $journal->fresh()->reversal_reason);
    }

    public function test_empty_journal_cannot_be_posted(): void
    {
        [$tenant, $organization, $actor] = $this->makeFinanceContext();

        $journal = JournalEntry::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'entry_number' => 'JE-NO-LINES',
            'date' => now()->toDateString(),
            'description' => 'Empty manual journal.',
            'total_debit' => 0,
            'total_credit' => 0,
            'is_balanced' => false,
            'is_posted' => false,
        ]);

        try {
            app(FinanceControlService::class)->postJournalEntry($journal, $actor);
            $this->fail('A zero-value journal with no lines must not be posted.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('at least two lines', $exception->getMessage());
        }

        $this->assertFalse((bool) $journal->fresh()->is_posted);
    }

    public function test_overpayment_is_rejected_before_changing_invoice_or_ledger(): void
    {
        [$tenant, $organization, $actor] = $this->makeFinanceContext();
        [$cashAccount, $receivableAccount] = $this->makeAccounts($tenant, $organization);

        TenantSetting::query()->create([
            'tenant_id' => $tenant->id,
            'group' => 'finance',
            'key' => 'default_receivable_account_id',
            'value' => (string) $receivableAccount->id,
            'type' => 'integer',
        ]);

        $invoice = StudentInvoice::query()->create([
            'tenant_id' => $tenant->id,
            'invoiceable_type' => Organization::class,
            'invoiceable_id' => $organization->id,
            'invoice_number' => 'INV-OVERPAY',
            'invoice_type' => 'student_invoice',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'amount' => 100000,
            'total_amount' => 100000,
            'paid_amount' => 0,
            'remaining_amount' => 100000,
            'status' => 'issued',
            'description' => 'Overpayment guard regression.',
        ]);

        $payment = Payment::query()->create([
            'tenant_id' => $tenant->id,
            'student_invoice_id' => $invoice->id,
            'chart_of_account_id' => $cashAccount->id,
            'payment_number' => 'PAY-OVERPAY',
            'payment_date' => now()->toDateString(),
            'amount' => 110000,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        try {
            app(FinanceControlService::class)->verifyPayment($payment, $actor);
            $this->fail('An overpayment must be rejected.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('exceeds the invoice remaining balance', $exception->getMessage());
        }

        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertSame('issued', $invoice->fresh()->status);
        $this->assertEqualsWithDelta(0, (float) $invoice->fresh()->paid_amount, 0.01);
        $this->assertSame(0, JournalEntry::query()->where('tenant_id', $tenant->id)->count());
    }

    protected function makeFinanceContext(): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'finance-control-plan',
            'name' => 'Finance Control Plan',
            'included_modules' => ['core', 'finance'],
        ]);

        $actor = User::query()->create([
            'name' => 'Finance Controller',
            'email' => 'finance-controller-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-finance-'.Str::lower(Str::random(5)),
            'name' => 'Finance Control Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $actor->id,
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => 'finance-org-'.Str::lower(Str::random(4)),
            'name' => 'Finance Organization',
        ]);

        return [$tenant, $organization, $actor];
    }

    protected function makeAccounts(Tenant $tenant, Organization $organization): array
    {
        $cashAccount = ChartOfAccount::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '1110',
            'name' => 'Cash and Bank',
            'level' => 1,
            'type' => 'asset',
            'category' => 'cash',
            'normal_balance' => 'debit',
            'is_bank_account' => true,
            'is_active' => true,
            'is_locked' => false,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $receivableAccount = ChartOfAccount::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '1130',
            'name' => 'Accounts Receivable',
            'level' => 1,
            'type' => 'asset',
            'category' => 'receivable',
            'normal_balance' => 'debit',
            'is_bank_account' => false,
            'is_active' => true,
            'is_locked' => false,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        return [$cashAccount, $receivableAccount];
    }
}
