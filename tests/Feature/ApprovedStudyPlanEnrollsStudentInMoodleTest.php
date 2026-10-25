<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
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

class ApprovedStudyPlanEnrollsStudentInMoodleTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_approved_study_plan_item_enqueues_enroll_outbox(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx, 'approved');

        $outbox = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT)
            ->where('entity_id', $item->id)
            ->first();

        $this->assertNotNull($outbox);
        $this->assertSame(MoodleOutboxService::ACTION_ENROLL, $outbox->action);
        $this->assertSame($ctx['user']->id, $outbox->payload['user_id']);
        $this->assertSame($item->course_offering_id, $outbox->payload['course_offering_id']);
    }

    public function test_draft_item_does_not_enqueue(): void
    {
        $ctx = $this->seedContext();
        $this->makeStudyPlanItem($ctx, 'draft');

        $this->assertSame(0, MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT)
            ->count());
    }

    public function test_status_transition_to_cancelled_enqueues_unenroll(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx, 'approved');
        MoodleSyncOutbox::query()->delete();

        $item->update(['status' => 'cancelled']);

        $latest = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT)
            ->where('entity_id', $item->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($latest);
        $this->assertSame(MoodleOutboxService::ACTION_UNENROLL, $latest->action);
    }

    public function test_deleting_item_enqueues_unenroll(): void
    {
        $ctx = $this->seedContext();
        $item = $this->makeStudyPlanItem($ctx, 'approved');
        MoodleSyncOutbox::query()->delete();

        $item->delete();

        $latest = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT)
            ->where('entity_id', $item->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($latest);
        $this->assertSame(MoodleOutboxService::ACTION_UNENROLL, $latest->action);
    }

    public function test_bulk_enroll_semester_enqueues_for_approved_items(): void
    {
        $ctx = $this->seedContext();
        $this->makeStudyPlanItem($ctx, 'approved');
        $this->makeStudyPlanItem($ctx, 'draft', classCode: 'B');

        MoodleSyncOutbox::query()->delete();

        Artisan::call('fos:moodle:bulk-enroll-semester', ['period' => $ctx['period']->id]);

        $bulkOutbox = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT)
            ->get();

        $this->assertCount(1, $bulkOutbox, 'only the approved item should be enqueued');
        $this->assertSame('bulk-enroll-semester', $bulkOutbox->first()->payload['source']);
    }

    protected function makeStudyPlanItem(array $ctx, string $status, string $classCode = 'A'): StudyPlanItem
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
            'total_credits' => 3,
            'status' => 'approved',
        ]);

        return StudyPlanItem::create([
            'tenant_id' => $ctx['tenant']->id,
            'study_plan_id' => $plan->id,
            'course_offering_id' => $offering->id,
            'course_id' => $ctx['course']->id,
            'credits' => 3,
            'status' => $status,
        ]);
    }

    protected function seedContext(): array
    {
        $rand = Str::random(4);

        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'krs-test'],
            ['name' => 'KRS Test', 'included_modules' => ['core', 'campus']],
        );

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "krs-{$rand}",
            'name' => 'Univ KRS',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "krs-org-{$rand}",
            'name' => 'Org KRS',
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
