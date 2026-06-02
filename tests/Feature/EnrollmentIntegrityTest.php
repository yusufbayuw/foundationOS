<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\ExamResult;
use Modules\Enrollment\Models\ExamSchedule;
use Modules\Enrollment\Models\Registration;
use Tests\TestCase;

class EnrollmentIntegrityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_exam_result_belongs_to_specific_exam_schedule(): void
    {
        [$tenant, $organization, $user] = $this->makeEnrollmentContext();

        $admissionPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB 2026',
            'code' => 'PPDB-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'registration_number' => 'REG-001',
            'full_name' => 'Calon Mahasiswa',
        ]);

        $examSchedule = ExamSchedule::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'name' => 'Tes Tulis Gelombang 1',
            'date' => '2026-02-10',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);

        $examResult = ExamResult::create([
            'tenant_id' => $tenant->id,
            'applicant_id' => $applicant->id,
            'exam_schedule_id' => $examSchedule->id,
            'examiner_id' => $user->id,
            'score' => 88.5,
            'is_passed' => true,
        ]);

        $this->assertSame($examSchedule->id, $examResult->examSchedule->id);
        $this->assertSame($examResult->id, $examSchedule->examResults->first()->id);
        $this->assertSame($examResult->id, $applicant->examResults->first()->id);
    }

    public function test_registration_is_unique_per_applicant(): void
    {
        [$tenant, $organization, $user] = $this->makeEnrollmentContext();

        $admissionPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB 2026',
            'code' => 'PPDB-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'registration_number' => 'REG-001',
            'full_name' => 'Calon Mahasiswa',
        ]);

        Registration::create([
            'tenant_id' => $tenant->id,
            'applicant_id' => $applicant->id,
            'completed_by' => $user->id,
            'registration_date' => '2026-02-15',
            'total_fee' => 250000,
        ]);

        $this->assertNotNull($applicant->registration);
        $this->assertSame(1, $admissionPeriod->registrations()->count());

        $this->expectException(QueryException::class);

        Registration::create([
            'tenant_id' => $tenant->id,
            'applicant_id' => $applicant->id,
            'completed_by' => $user->id,
            'registration_date' => '2026-02-16',
            'total_fee' => 250000,
        ]);
    }

    public function test_admission_period_counters_sync_with_applicant_changes(): void
    {
        [$tenant, $organization] = $this->makeEnrollmentContext();

        $firstPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Gelombang 1',
            'code' => 'G1',
            'start_date' => '2026-01-01',
            'end_date' => '2026-02-01',
        ]);

        $secondPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Gelombang 2',
            'code' => 'G2',
            'start_date' => '2026-02-02',
            'end_date' => '2026-03-01',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $firstPeriod->id,
            'registration_number' => 'REG-COUNT-1',
            'full_name' => 'Calon 1',
        ]);

        $this->assertSame(1, $firstPeriod->fresh()->registered_count);
        $this->assertSame(0, $firstPeriod->fresh()->accepted_count);

        $applicant->update([
            'is_passed' => true,
        ]);

        $this->assertSame(1, $firstPeriod->fresh()->accepted_count);

        $applicant->update([
            'admission_period_id' => $secondPeriod->id,
        ]);

        $this->assertSame(0, $firstPeriod->fresh()->registered_count);
        $this->assertSame(0, $firstPeriod->fresh()->accepted_count);
        $this->assertSame(1, $secondPeriod->fresh()->registered_count);
        $this->assertSame(1, $secondPeriod->fresh()->accepted_count);

        $applicant->delete();

        $this->assertSame(0, $secondPeriod->fresh()->registered_count);
        $this->assertSame(0, $secondPeriod->fresh()->accepted_count);
    }

    public function test_exam_schedule_registered_count_syncs_with_exam_results(): void
    {
        [$tenant, $organization, $user] = $this->makeEnrollmentContext();

        $admissionPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB 2026',
            'code' => 'PPDB-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'registration_number' => 'REG-RESULT-1',
            'full_name' => 'Calon Mahasiswa',
        ]);

        $firstSchedule = ExamSchedule::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'name' => 'Tes Tulis',
            'date' => '2026-02-10',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);

        $secondSchedule = ExamSchedule::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'name' => 'Tes Wawancara',
            'date' => '2026-02-11',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);

        $result = ExamResult::create([
            'tenant_id' => $tenant->id,
            'applicant_id' => $applicant->id,
            'exam_schedule_id' => $firstSchedule->id,
            'examiner_id' => $user->id,
            'score' => 80,
        ]);

        $this->assertSame(1, $firstSchedule->fresh()->registered_count);
        $this->assertSame(0, $secondSchedule->fresh()->registered_count);

        $result->update([
            'exam_schedule_id' => $secondSchedule->id,
        ]);

        $this->assertSame(0, $firstSchedule->fresh()->registered_count);
        $this->assertSame(1, $secondSchedule->fresh()->registered_count);

        $result->delete();

        $this->assertSame(0, $secondSchedule->fresh()->registered_count);
    }

    public function test_applicant_summary_scores_sync_from_exam_results(): void
    {
        [$tenant, $organization, $user] = $this->makeEnrollmentContext();

        $admissionPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB 2026',
            'code' => 'PPDB-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'registration_number' => 'REG-SUMMARY-1',
            'full_name' => 'Calon Mahasiswa',
        ]);

        $testSchedule = ExamSchedule::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'name' => 'Tes Tulis',
            'type' => 'test',
            'date' => '2026-02-10',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);

        $interviewSchedule = ExamSchedule::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'name' => 'Wawancara',
            'type' => 'interview',
            'date' => '2026-02-11',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);

        $testResult = ExamResult::create([
            'tenant_id' => $tenant->id,
            'applicant_id' => $applicant->id,
            'exam_schedule_id' => $testSchedule->id,
            'examiner_id' => $user->id,
            'score' => 80,
            'is_passed' => true,
        ]);

        $interviewResult = ExamResult::create([
            'tenant_id' => $tenant->id,
            'applicant_id' => $applicant->id,
            'exam_schedule_id' => $interviewSchedule->id,
            'examiner_id' => $user->id,
            'score' => 90,
            'is_passed' => true,
        ]);

        $this->assertSame(80.0, (float) $applicant->fresh()->test_score);
        $this->assertSame(90.0, (float) $applicant->fresh()->interview_score);
        $this->assertSame(85.0, (float) $applicant->fresh()->final_score);
        $this->assertTrue((bool) $applicant->fresh()->is_passed);

        $interviewResult->update([
            'score' => 70,
            'is_passed' => false,
        ]);

        $this->assertSame(70.0, (float) $applicant->fresh()->interview_score);
        $this->assertSame(75.0, (float) $applicant->fresh()->final_score);
        $this->assertFalse((bool) $applicant->fresh()->is_passed);

        $testResult->delete();

        $this->assertNull($applicant->fresh()->test_score);
        $this->assertSame(70.0, (float) $applicant->fresh()->interview_score);
        $this->assertSame(70.0, (float) $applicant->fresh()->final_score);
    }

    private function makeEnrollmentContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'enrollment-suite',
            'name' => 'Enrollment Suite',
            'included_modules' => ['core', 'enrollment'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-enrollment',
            'name' => 'Tenant Enrollment',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-enrollment',
            'name' => 'Org Enrollment',
        ]);

        $user = User::create([
            'name' => 'Petugas PPDB',
            'username' => 'petugas-ppdb',
            'email' => 'ppdb@example.com',
            'password' => 'secret',
        ]);

        return [$tenant, $organization, $user];
    }
}
