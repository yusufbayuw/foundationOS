<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Enrollment\Events\ApplicantAccepted;
use Modules\Enrollment\Events\ApplicantAcceptanceReverted;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Services\ApplicantPromotionService;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Services\ApplicantOnboardingInvoiceService;
use Modules\Library\Models\Member;
use Modules\Library\Services\LibraryMemberProvisioningService;
use Modules\School\Models\Student;
use Modules\School\Listeners\CreateStudentFromAcceptedApplicant;
use Modules\Finance\Listeners\CreateInitialInvoiceFromAcceptedApplicant;
use Modules\Library\Listeners\CreateLibraryMemberFromAcceptedApplicant;
use Tests\TestCase;

class ApplicantAcceptedPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_applicant_dispatches_event(): void
    {
        Event::fake([ApplicantAccepted::class]);

        [$tenant, $organization, $applicant] = $this->makeApplicant('registered');

        $applicant->forceFill(['status' => 'accepted'])->save();

        Event::assertDispatched(ApplicantAccepted::class, function (ApplicantAccepted $event) use ($applicant) {
            return $event->applicant->id === $applicant->id;
        });
    }

    public function test_promotion_service_creates_student_and_links_applicant(): void
    {
        [$tenant, $organization, $applicant] = $this->makeApplicant('accepted', 500_000);

        $student = app(ApplicantPromotionService::class)->promote($applicant);

        $this->assertNotNull($student);
        $this->assertSame('active', $student->status);
        $this->assertStringStartsWith('ADM-', $student->nis);

        $applicant->refresh();
        $this->assertSame($student->id, $applicant->converted_to_student_id);
    }

    public function test_invoice_service_is_idempotent(): void
    {
        [$tenant, $organization, $applicant] = $this->makeApplicant('accepted', 750_000);

        $service = app(ApplicantOnboardingInvoiceService::class);
        $first = $service->createDraftFor($applicant);
        $second = $service->createDraftFor($applicant);

        $this->assertNotNull($first);
        $this->assertSame($first->id, $second->id);
        $this->assertEqualsWithDelta(750_000.0, (float) $first->total_amount, 0.01);
        $this->assertSame(1, StudentInvoice::withoutTenantScope()
            ->where('invoiceable_id', $applicant->id)
            ->count());
    }

    public function test_listeners_create_student_invoice_and_member(): void
    {
        [$tenant, $organization, $applicant] = $this->makeApplicant('accepted', 250_000);

        app(CurrentTenant::class)->set($tenant);

        $event = new ApplicantAccepted($applicant->fresh(['admissionPeriod']), null);

        (new CreateStudentFromAcceptedApplicant)->handle($event);
        (new CreateInitialInvoiceFromAcceptedApplicant)->handle($event);
        (new CreateLibraryMemberFromAcceptedApplicant)->handle(
            new ApplicantAccepted($applicant->fresh(['admissionPeriod', 'convertedStudent']), null),
        );

        $applicant->refresh();

        $this->assertNotNull($applicant->converted_to_student_id);
        $this->assertSame(1, Student::withoutTenantScope()->where('tenant_id', $tenant->id)->count());
        $this->assertSame(1, StudentInvoice::withoutTenantScope()
            ->where('invoiceable_id', $applicant->id)
            ->count());
        $this->assertSame(1, Member::withoutTenantScope()
            ->where('member_number', $applicant->registration_number)
            ->count());

        app(CurrentTenant::class)->forget();
    }

    public function test_reverting_acceptance_voids_open_invoices(): void
    {
        [$tenant, $organization, $applicant] = $this->makeApplicant('accepted', 100_000);

        app(ApplicantOnboardingInvoiceService::class)->createDraftFor($applicant);

        Event::fake([ApplicantAcceptanceReverted::class]);

        $applicant->forceFill(['status' => 'registered'])->save();

        Event::assertDispatched(ApplicantAcceptanceReverted::class);

        $invoice = StudentInvoice::withoutTenantScope()
            ->where('invoiceable_id', $applicant->id)
            ->first();

        $this->assertNotNull($invoice);

        (new \Modules\Enrollment\Listeners\CompensateApplicantAcceptance)
            ->handle(new ApplicantAcceptanceReverted($applicant->fresh(), 'accepted', null));

        $this->assertSame('void', $invoice->fresh()->status);
    }

    public function test_tenant_setting_can_disable_auto_promotion(): void
    {
        [$tenant, $organization, $applicant] = $this->makeApplicant('accepted', 0);

        TenantSetting::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'group' => ApplicantPromotionService::SETTING_GROUP,
            'key' => ApplicantPromotionService::SETTING_KEY,
            'value' => '0',
            'type' => 'boolean',
        ]);

        $this->assertNull(app(ApplicantPromotionService::class)->promote($applicant));
        $this->assertNull($applicant->fresh()->converted_to_student_id);
    }

    /**
     * @return array{0:Tenant,1:Organization,2:Applicant}
     */
    protected function makeApplicant(string $status, float $registrationFee = 0): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'applicant-pipeline-test'],
            ['name' => 'Applicant Pipeline Test', 'included_modules' => ['core', 'enrollment', 'school', 'finance', 'library']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "adm-{$rand}",
            'name' => "Admission Tenant {$rand}",
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus',
            'code' => "C-{$rand}",
        ]);

        $period = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB 2026',
            'code' => 'PPDB-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
            'registration_fee' => $registrationFee,
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $period->id,
            'registration_number' => 'REG-'.$rand,
            'full_name' => 'Calon Siswa',
            'status' => $status,
        ]);

        return [$tenant, $organization, $applicant];
    }
}
