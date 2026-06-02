<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleDetailedGradePullService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Services\CampusGpaCalculator;
use Modules\Campus\Services\GradebookConfigResolver;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Tests\TestCase;

class DetailedGradePullPopulatesStudyResultTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_grade_items_are_mapped_to_breakdown_and_weighted_score(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx);

        $service = app(MoodleDetailedGradePullService::class);

        $gradeItems = [
            ['itemname' => 'UTS Calculus', 'graderaw' => 80, 'grademax' => 100],
            ['itemname' => 'UAS Calculus', 'graderaw' => 90, 'grademax' => 100],
            ['itemname' => 'Tugas 1', 'graderaw' => 70, 'grademax' => 100],
            ['itemname' => 'Kehadiran', 'graderaw' => 100, 'grademax' => 100],
        ];

        $result = $service->processGradeItems($item, $gradeItems);

        $this->assertSame($item->id, $result->study_plan_item_id);
        $breakdown = $result->components_breakdown;
        $this->assertArrayHasKey('midterm', $breakdown);
        $this->assertArrayHasKey('final', $breakdown);
        $this->assertArrayHasKey('assignment', $breakdown);
        $this->assertArrayHasKey('attendance', $breakdown);

        $this->assertSame(80.0, (float) $breakdown['midterm']['score']);
        $this->assertSame(90.0, (float) $breakdown['final']['score']);

        // Weighted: 100*0.10 + 70*0.20 + 80*0.30 + 90*0.40 = 10 + 14 + 24 + 36 = 84.0
        $this->assertEqualsWithDelta(84.0, (float) $result->weight_score, 0.01);

        // Default scale: 84.0 → A- (≥80, <85)
        $this->assertSame('A-', $result->grade_letter);
        $this->assertEqualsWithDelta(3.7, (float) $result->grade_point, 0.01);
        $this->assertTrue($result->passed);
        $this->assertNotNull($result->moodle_pulled_at);
        $this->assertSame('moodle', $result->source);
    }

    public function test_partial_components_normalize_by_used_weight(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx);

        $service = app(MoodleDetailedGradePullService::class);

        // Only UTS + UAS — no attendance/assignment. Weighted = (60*0.30 + 80*0.40) / (0.70) = 50/0.70 = ~71.43
        $result = $service->processGradeItems($item, [
            ['itemname' => 'UTS', 'graderaw' => 60, 'grademax' => 100],
            ['itemname' => 'UAS', 'graderaw' => 80, 'grademax' => 100],
        ]);

        $this->assertEqualsWithDelta(71.43, (float) $result->weight_score, 0.05);
        $this->assertSame('B', $result->grade_letter);
    }

    public function test_re_pull_updates_existing_study_result(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx);

        $service = app(MoodleDetailedGradePullService::class);

        $service->processGradeItems($item, [
            ['itemname' => 'UAS', 'graderaw' => 50, 'grademax' => 100],
        ]);

        $first = StudyResult::withoutTenantScope()->where('study_plan_item_id', $item->id)->first();
        $this->assertSame('D', $first->grade_letter);

        $service->processGradeItems($item, [
            ['itemname' => 'UAS', 'graderaw' => 95, 'grademax' => 100],
        ]);

        $this->assertSame(1, StudyResult::withoutTenantScope()->where('study_plan_item_id', $item->id)->count());
        $second = StudyResult::withoutTenantScope()->where('study_plan_item_id', $item->id)->first();
        $this->assertSame('A', $second->grade_letter);
    }

    public function test_tenant_can_override_components_via_setting(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx);

        TenantSetting::withoutTenantScope()->create([
            'tenant_id' => $ctx['tenant']->id,
            'group' => GradebookConfigResolver::SETTING_GROUP,
            'key' => GradebookConfigResolver::SETTING_KEY_COMPONENTS,
            'value' => json_encode([
                'midterm' => ['weight' => 0.5, 'matchers' => ['uts']],
                'final' => ['weight' => 0.5, 'matchers' => ['uas']],
            ]),
            'type' => 'json',
        ]);

        $service = app(MoodleDetailedGradePullService::class);
        // 50-50 split, UTS=70 UAS=80 → 75
        $result = $service->processGradeItems($item, [
            ['itemname' => 'UTS', 'graderaw' => 70, 'grademax' => 100],
            ['itemname' => 'UAS', 'graderaw' => 80, 'grademax' => 100],
        ]);

        $this->assertEqualsWithDelta(75.0, (float) $result->weight_score, 0.01);
        $this->assertSame('B+', $result->grade_letter);
    }

    public function test_gpa_calculator_weights_by_credits(): void
    {
        $ctx = $this->seedContext();
        $item3sks = $this->makeStudyPlanItem($ctx, credits: 3);
        $item2sks = $this->makeStudyPlanItem($ctx, credits: 2, classCode: 'B');

        StudyResult::create([
            'tenant_id' => $ctx['tenant']->id,
            'study_plan_item_id' => $item3sks->id,
            'grade_letter' => 'A',
            'grade_point' => 4.0,
            'weight_score' => 90,
            'passed' => true,
        ]);

        StudyResult::create([
            'tenant_id' => $ctx['tenant']->id,
            'study_plan_item_id' => $item2sks->id,
            'grade_letter' => 'C',
            'grade_point' => 2.0,
            'weight_score' => 55,
            'passed' => true,
        ]);

        // (3*4.0 + 2*2.0) / 5 = 16/5 = 3.20
        $gpa = app(CampusGpaCalculator::class)->recalculateForStudent($ctx['student']->id);

        $this->assertEqualsWithDelta(3.20, $gpa, 0.01);
        $this->assertEqualsWithDelta(3.20, (float) $ctx['student']->fresh()->gpa_cached, 0.01);
    }

    protected function makeStudyPlanItem(array $ctx, int $credits = 3, string $classCode = 'A'): StudyPlanItem
    {
        $offering = CourseOffering::create([
            'tenant_id' => $ctx['tenant']->id,
            'organization_id' => $ctx['organization']->id,
            'course_id' => $ctx['course']->id,
            'academic_period_id' => $ctx['period']->id,
            'class_code' => $classCode,
            'capacity' => 30,
            'status' => 'open',
        ]);

        $plan = StudyPlan::create([
            'tenant_id' => $ctx['tenant']->id,
            'collage_student_id' => $ctx['student']->id,
            'academic_period_id' => $ctx['period']->id,
            'plan_number' => 'KRS-'.Str::random(4).$classCode,
            'total_credits' => $credits,
            'status' => 'approved',
        ]);

        return StudyPlanItem::create([
            'tenant_id' => $ctx['tenant']->id,
            'study_plan_id' => $plan->id,
            'course_offering_id' => $offering->id,
            'course_id' => $ctx['course']->id,
            'credits' => $credits,
            'status' => 'approved',
        ]);
    }

    protected function seedContext(): array
    {
        $rand = Str::random(4);

        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'grade-test'],
            ['name' => 'Grade Test', 'included_modules' => ['core', 'campus']],
        );

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "grd-{$rand}",
            'name' => 'Univ Grade',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "grd-org-{$rand}",
            'name' => 'Org Grade',
        ]);

        $user = User::create([
            'name' => 'Mahasiswa '.$rand,
            'email' => "mhs-{$rand}@example.com",
            'password' => 'password',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => "y-{$rand}",
            'name' => 'Year',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $year->id,
            'code' => "p-{$rand}",
            'name' => 'Period',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'is_active' => true,
        ]);

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => "FT-{$rand}",
            'name' => 'Faculty',
        ]);

        $studyProgram = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'faculty_id' => $faculty->id,
            'code' => "SI-{$rand}",
            'name' => 'Information Systems',
            'total_credits_required' => 144,
        ]);

        $student = CollageStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'study_program_id' => $studyProgram->id,
            'student_number' => '2026'.Str::random(4),
        ]);

        $course = Course::create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $studyProgram->id,
            'code' => "IF-{$rand}",
            'name' => 'Algorithms',
            'credits' => 3,
        ]);

        return [
            'tenant' => $tenant,
            'organization' => $organization,
            'user' => $user,
            'period' => $period,
            'studyProgram' => $studyProgram,
            'student' => $student,
            'course' => $course,
        ];
    }
}
