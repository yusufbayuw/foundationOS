<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleStudyPlanSyncService;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class MoodleStudyPlanSyncServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_approving_a_study_plan_enqueues_enroll_per_item(): void
    {
        $ctx = $this->seedPlanWithItems(2);

        $ctx['plan']->forceFill(['status' => 'approved'])->save();

        foreach ($ctx['items'] as $item) {
            $this->assertSame(1, MoodleSyncOutbox::query()
                ->where('dedupe_key', "krs:enrol:{$item->id}")
                ->where('action', 'enroll')
                ->count());
        }
    }

    public function test_cancelling_a_study_plan_enqueues_unenroll_per_item(): void
    {
        $ctx = $this->seedPlanWithItems(1);
        $ctx['plan']->forceFill(['status' => 'approved'])->save();

        $ctx['plan']->forceFill(['status' => 'cancelled'])->save();

        $item = $ctx['items'][0];
        $this->assertSame(1, MoodleSyncOutbox::query()
            ->where('dedupe_key', "krs:unenrol:{$item->id}")
            ->where('action', 'unenroll')
            ->count());
    }

    public function test_bulk_enroll_semester_processes_only_approved_plans(): void
    {
        $ctx = $this->seedPlanWithItems(2);
        $ctx['plan']->forceFill(['status' => 'approved'])->save();

        // Wipe outbox to isolate bulk run.
        MoodleSyncOutbox::query()->delete();

        $count = app(MoodleStudyPlanSyncService::class)
            ->bulkEnrollSemester((int) $ctx['period']->id);

        $this->assertSame(2, $count);
        $this->assertSame(2, MoodleSyncOutbox::query()->where('action', 'enroll')->count());
    }

    public function test_item_without_course_offering_is_skipped(): void
    {
        $ctx = $this->seedPlanWithItems(0);
        $item = StudyPlanItem::query()->create([
            'tenant_id' => $ctx['tenant']->id,
            'study_plan_id' => $ctx['plan']->id,
            'course_offering_id' => null,
            'course_id' => $ctx['course']->id,
            'credits' => 3,
            'status' => 'enrolled',
        ]);

        $this->assertFalse(
            app(MoodleStudyPlanSyncService::class)->enrollItem($item->fresh()),
        );
        $this->assertSame(0, MoodleSyncOutbox::query()
            ->where('dedupe_key', "krs:enrol:{$item->id}")
            ->count());
    }

    protected function seedPlanWithItems(int $itemCount): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'krs-test'],
            ['name' => 'KRS', 'included_modules' => ['core', 'campus']],
        );
        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "krs-{$rand}",
            'name' => 'KRS Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "krs-org-{$rand}",
            'name' => 'KRS Org',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'code' => "ky-{$rand}",
            'name' => 'Year',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'is_active' => true,
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'code' => "kp-{$rand}",
            'name' => 'Period',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'is_active' => true,
        ]);

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => "fac-{$rand}",
            'name' => 'Faculty',
        ]);

        $program = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'faculty_id' => $faculty->id,
            'code' => "prog-{$rand}",
            'name' => 'Program',
            'degree_level' => 's1',
        ]);

        $user = User::create([
            'name' => 'KRS Student',
            'email' => "krs-stu-{$rand}@example.com",
            'password' => 'password',
        ]);

        $student = CollageStudent::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'study_program_id' => $program->id,
            'user_id' => $user->id,
            'student_number' => "NIM-{$rand}",
            'enrollment_year' => 2026,
            'status' => 'active',
        ]);

        $course = Course::query()->create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $program->id,
            'code' => "C-{$rand}",
            'name' => 'Course',
            'credits' => 3,
            'semester_level' => 1,
            'is_active' => true,
        ]);

        $plan = StudyPlan::query()->create([
            'tenant_id' => $tenant->id,
            'collage_student_id' => $student->id,
            'academic_period_id' => $period->id,
            'plan_number' => "KRS-{$rand}",
            'total_credits' => 0,
            'status' => 'submitted',
        ]);

        $items = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $offering = CourseOffering::query()->create([
                'tenant_id' => $tenant->id,
                'organization_id' => $org->id,
                'course_id' => $course->id,
                'academic_period_id' => $period->id,
                'class_code' => "A{$i}-{$rand}",
                'capacity' => 30,
                'delivery_mode' => 'offline',
                'status' => 'active',
            ]);
            $items[] = StudyPlanItem::query()->create([
                'tenant_id' => $tenant->id,
                'study_plan_id' => $plan->id,
                'course_offering_id' => $offering->id,
                'course_id' => $course->id,
                'credits' => 3,
                'status' => 'enrolled',
            ]);
        }

        return compact('tenant', 'org', 'period', 'program', 'course', 'student', 'plan', 'items');
    }
}
