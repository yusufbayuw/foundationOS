<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Core\Models\ChartOfAccount;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\Registration;
use Modules\Enrollment\Listeners\UpdateRegistrationOnInvoicePaid;
use Modules\Finance\Events\StudentInvoicePaid;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Services\FinanceControlService;
use Tests\TestCase;

class StudentInvoicePaidPipelineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_recalculate_dispatches_event_when_invoice_becomes_paid(): void
    {
        Event::fake([StudentInvoicePaid::class]);

        [$tenant, $invoice, $actor] = $this->makePaidInvoiceContext(500_000);

        app(FinanceControlService::class)->recalculateInvoice($invoice->fresh(), $actor);

        Event::assertDispatched(StudentInvoicePaid::class, function (StudentInvoicePaid $event) use ($invoice) {
            return $event->invoice->id === $invoice->id;
        });
    }

    public function test_listener_marks_registration_paid(): void
    {
        [$tenant, $invoice, $actor, $applicant, $registration] = $this->makePaidInvoiceContext(500_000, withRegistration: true);

        app(CurrentTenant::class)->set($tenant);

        $invoice->forceFill([
            'status' => 'paid',
            'paid_amount' => 500_000,
            'remaining_amount' => 0,
        ])->save();

        (new UpdateRegistrationOnInvoicePaid)->handle(new StudentInvoicePaid($invoice->fresh(), $actor));

        $registration->refresh();

        $this->assertSame('paid', $registration->payment_status);
        $this->assertSame('confirmed', $registration->status);

        app(CurrentTenant::class)->forget();
    }

    /**
     * @return array{0:Tenant,1:StudentInvoice,2:User,3?:Applicant,4?:Registration}
     */
    protected function makePaidInvoiceContext(float $amount, bool $withRegistration = false): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'invoice-paid-test'],
            ['name' => 'Invoice Paid Test', 'included_modules' => ['core', 'finance', 'enrollment']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "inv-{$rand}",
            'name' => "Finance Tenant {$rand}",
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus',
            'code' => "C-{$rand}",
        ]);

        $actor = User::create([
            'name' => 'Finance Staff',
            'email' => "finance-{$rand}@example.com",
            'password' => 'password',
        ]);

        $period = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB',
            'code' => 'PPDB',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-'.$rand,
            'full_name' => 'Calon',
            'status' => 'accepted',
        ]);

        $invoice = StudentInvoice::create([
            'tenant_id' => $tenant->id,
            'invoiceable_type' => $applicant->getMorphClass(),
            'invoiceable_id' => $applicant->id,
            'invoice_number' => 'INV-'.$rand,
            'invoice_type' => 'registration',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'amount' => $amount,
            'total_amount' => $amount,
            'paid_amount' => 0,
            'remaining_amount' => $amount,
            'status' => 'issued',
        ]);

        ChartOfAccount::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '1100',
            'name' => 'Cash',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $cash = ChartOfAccount::query()->where('tenant_id', $tenant->id)->first();

        ChartOfAccount::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '1200',
            'name' => 'Receivable',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $receivable = ChartOfAccount::query()->where('code', '1200')->first();

        Payment::create([
            'tenant_id' => $tenant->id,
            'student_invoice_id' => $invoice->id,
            'chart_of_account_id' => $cash->id,
            'payment_number' => 'PAY-'.$rand,
            'payment_date' => now()->toDateString(),
            'amount' => $amount,
            'status' => 'verified',
            'verified_by' => $actor->id,
            'verified_at' => now(),
        ]);

        $registration = null;

        if ($withRegistration) {
            $registration = Registration::create([
                'tenant_id' => $tenant->id,
                'applicant_id' => $applicant->id,
                'completed_by' => $actor->id,
                'registration_date' => now()->toDateString(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_fee' => $amount,
            ]);
        }

        if ($withRegistration) {
            return [$tenant, $invoice, $actor, $applicant, $registration];
        }

        return [$tenant, $invoice, $actor];
    }
}
