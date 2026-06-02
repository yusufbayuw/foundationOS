<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Department;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\ExamSchedule;
use Modules\Enrollment\Models\Registration;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class EnrollmentDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use LazilyRefreshDatabase;

    public function test_guest_cannot_download_acceptance_letter_pdf(): void
    {
        $applicant = $this->makeAcceptedApplicant();

        $this->getJson(route('enrollment.applicants.acceptance.pdf', $applicant))
            ->assertUnauthorized();
    }

    public function test_super_admin_can_download_acceptance_letter_pdf(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $applicant = $this->makeAcceptedApplicant();

        $this->actingAs($user)
            ->get(route('enrollment.applicants.acceptance.pdf', $applicant))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_pending_applicant_acceptance_pdf_is_forbidden(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $applicant = $this->makeAcceptedApplicant(status: 'registered');

        $this->actingAs($user)
            ->get(route('enrollment.applicants.acceptance.pdf', $applicant))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_rejection_letter_pdf(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $applicant = $this->makeAcceptedApplicant(status: 'rejected');

        $this->actingAs($user)
            ->get(route('enrollment.applicants.rejection.pdf', $applicant))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_accepted_applicant_rejection_pdf_is_forbidden(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $applicant = $this->makeAcceptedApplicant();

        $this->actingAs($user)
            ->get(route('enrollment.applicants.rejection.pdf', $applicant))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_registration_proof_pdf(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $registration = $this->makeRegistration(status: 'completed');

        $this->actingAs($user)
            ->get(route('enrollment.registrations.pdf', $registration))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_pending_registration_pdf_is_forbidden(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $registration = $this->makeRegistration(status: 'pending');

        $this->actingAs($user)
            ->get(route('enrollment.registrations.pdf', $registration))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_exam_schedule_pdf(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $schedule = $this->makeExamSchedule();

        $this->actingAs($user)
            ->get(route('enrollment.exam-schedules.pdf', $schedule))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_inactive_exam_schedule_pdf_is_forbidden(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $schedule = $this->makeExamSchedule(isActive: false);

        $this->actingAs($user)
            ->get(route('enrollment.exam-schedules.pdf', $schedule))
            ->assertForbidden();
    }

    private function makeEnrollmentContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'enrollment-pdf',
            'name' => 'Enrollment PDF',
            'included_modules' => ['core', 'enrollment'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'enrollment-pdf-tenant',
            'name' => 'Enrollment PDF Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_main' => true,
        ]);

        $department = Department::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'IPA',
            'name' => 'Natural Science',
        ]);

        $admissionPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'PPDB-2026',
            'name' => 'PPDB 2026',
            'type' => 'new_student',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'announcement_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        return [$tenant, $organization, $department, $admissionPeriod];
    }

    private function makeAcceptedApplicant(string $status = 'accepted'): Applicant
    {
        [$tenant, , $department, $admissionPeriod] = $this->makeEnrollmentContext();

        return Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'registration_number' => 'REG-'.Str::upper(Str::random(4)),
            'full_name' => 'Calon Siswa PDF',
            'status' => $status,
            'accepted_program_id' => $department->id,
            'program_choice_1_id' => $department->id,
        ]);
    }

    private function makeRegistration(string $status = 'completed'): Registration
    {
        $applicant = $this->makeAcceptedApplicant();

        return Registration::create([
            'tenant_id' => $applicant->tenant_id,
            'applicant_id' => $applicant->id,
            'registration_date' => now()->toDateString(),
            'status' => $status,
            'payment_status' => 'paid',
            'total_fee' => 1500000,
            'paid_amount' => 1500000,
            'completed_at' => now(),
        ]);
    }

    private function makeExamSchedule(bool $isActive = true): ExamSchedule
    {
        [$tenant, , , $admissionPeriod] = $this->makeEnrollmentContext();

        return ExamSchedule::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'name' => 'Tes Tulis Gelombang 1',
            'type' => 'test',
            'date' => now()->addWeek()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'location' => 'Ruang Ujian A',
            'room_capacity' => 50,
            'is_active' => $isActive,
        ]);
    }
}
