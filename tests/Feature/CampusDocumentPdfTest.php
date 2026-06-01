<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Models\Thesis;
use Modules\Campus\Models\Wisuda;
use Modules\Campus\Models\Yudisium;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class CampusDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use RefreshDatabase;

    public function test_guest_cannot_download_transcript_pdf(): void
    {
        $student = $this->makeCollageStudent();

        $this->getJson(route('campus.transcript.pdf', $student))
            ->assertUnauthorized();
    }

    public function test_super_admin_can_download_transcript_pdf(): void
    {
        [, , $user, $student] = $this->makeCampusPdfContext();
        $this->seedPublishedStudyResult($student);

        $this->actingAs($user)
            ->get(route('campus.transcript.pdf', $student))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_draft_study_plan_pdf_is_forbidden(): void
    {
        [, , $user, $student] = $this->makeCampusPdfContext();

        $plan = StudyPlan::create([
            'tenant_id' => $student->tenant_id,
            'collage_student_id' => $student->id,
            'plan_number' => 'KRS-DRAFT',
            'total_credits' => 3,
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->get(route('campus.study-plans.pdf', $plan))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_approved_study_plan_pdf(): void
    {
        [, , $user, $student, $plan] = $this->makeCampusPdfContext(withApprovedPlan: true);

        $this->actingAs($user)
            ->get(route('campus.study-plans.pdf', $plan))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_published_study_result_pdf(): void
    {
        [, , $user, $student] = $this->makeCampusPdfContext();
        $result = $this->seedPublishedStudyResult($student);

        $this->actingAs($user)
            ->get(route('campus.study-results.pdf', $result))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_thesis_pdf(): void
    {
        [, , $user, $student] = $this->makeCampusPdfContext();

        $thesis = Thesis::create([
            'tenant_id' => $student->tenant_id,
            'collage_student_id' => $student->id,
            'title' => 'Sistem Informasi Terdistribusi',
            'status' => 'in_progress',
        ]);

        $this->actingAs($user)
            ->get(route('campus.theses.pdf', $thesis))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_wisuda_and_yudisium_pdfs(): void
    {
        [$tenant, $organization, $user] = $this->makeCampusPdfContext();

        $yudisium = Yudisium::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Yudisium Genap 2026',
            'held_at' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $wisuda = Wisuda::create([
            'tenant_id' => $tenant->id,
            'yudisium_id' => $yudisium->id,
            'name' => 'Wisuda ke-50',
            'held_at' => now()->addWeek()->toDateString(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->get(route('campus.yudisiums.pdf', $yudisium))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('campus.wisudas.pdf', $wisuda))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User, 3: CollageStudent, 4?: StudyPlan}
     */
    private function makeCampusPdfContext(bool $withApprovedPlan = false): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'campus-pdf',
            'name' => 'Campus PDF',
            'included_modules' => ['core', 'campus'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'campus-pdf-tenant',
            'name' => 'Campus PDF Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Campus Org',
            'is_main' => true,
        ]);

        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $student = $this->makeCollageStudent($tenant, $organization, $user);

        if (! $withApprovedPlan) {
            return [$tenant, $organization, $user, $student];
        }

        $studyPlan = $this->makeApprovedStudyPlan($tenant, $organization, $student);

        return [$tenant, $organization, $user, $student, $studyPlan];
    }

    private function makeCollageStudent(?Tenant $tenant = null, ?Organization $organization = null, ?User $user = null): CollageStudent
    {
        if ($tenant === null) {
            [, , , $student] = $this->makeCampusPdfContext();

            return $student;
        }

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'FT',
            'name' => 'Faculty of Tech',
        ]);

        $studyProgram = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'faculty_id' => $faculty->id,
            'code' => 'SI',
            'name' => 'Information Systems',
        ]);

        return CollageStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user?->id,
            'study_program_id' => $studyProgram->id,
            'student_number' => '2026'.Str::random(4),
            'full_name' => 'Mahasiswa PDF',
            'status' => 'active',
        ]);
    }

    private function makeApprovedStudyPlan(Tenant $tenant, Organization $organization, CollageStudent $student): StudyPlan
    {
        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '2025-26',
            'name' => '2025/2026',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $year->id,
            'code' => 'G-2026',
            'name' => 'Genap 2026',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'is_active' => true,
        ]);

        $course = Course::create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $student->study_program_id,
            'code' => 'IF101',
            'name' => 'Algorithms',
            'credits' => 3,
        ]);

        $offering = CourseOffering::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'course_id' => $course->id,
            'academic_period_id' => $period->id,
            'class_code' => 'A',
            'capacity' => 30,
        ]);

        $plan = StudyPlan::create([
            'tenant_id' => $tenant->id,
            'collage_student_id' => $student->id,
            'academic_period_id' => $period->id,
            'plan_number' => 'KRS-APPROVED',
            'total_credits' => 3,
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        StudyPlanItem::create([
            'tenant_id' => $tenant->id,
            'study_plan_id' => $plan->id,
            'course_offering_id' => $offering->id,
            'course_id' => $course->id,
            'credits' => 3,
        ]);

        return $plan;
    }

    private function seedPublishedStudyResult(CollageStudent $student): StudyResult
    {
        $plan = $this->makeApprovedStudyPlan(
            Tenant::query()->findOrFail($student->tenant_id),
            Organization::query()->findOrFail($student->organization_id),
            $student,
        );

        $item = $plan->items()->first();

        return StudyResult::create([
            'tenant_id' => $student->tenant_id,
            'study_plan_item_id' => $item->id,
            'grade_letter' => 'A',
            'grade_point' => 4.0,
            'weight_score' => 4.0,
            'passed' => true,
            'published_at' => now(),
        ]);
    }
}
