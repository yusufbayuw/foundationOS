<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncService;
use App\Models\MoodleEntityMapping;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Enums\CourseOfferingLecturerRole;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\CourseOfferingLecturer;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class LecturerAssignmentSyncsAsMoodleTeacherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_primary_lecturer_assignment_enqueues_assign_outbox_with_editing_teacher_role(): void
    {
        $ctx = $this->seedContext();

        $assignment = CourseOfferingLecturer::create([
            'tenant_id' => $ctx['tenant']->id,
            'course_offering_id' => $ctx['offering']->id,
            'lecturer_id' => $ctx['lecturer']->id,
            'role' => CourseOfferingLecturerRole::Primary,
            'is_active' => true,
            'assigned_at' => now(),
        ]);

        $outbox = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_LECTURER_ASSIGNMENT)
            ->where('entity_id', $assignment->id)
            ->first();

        $this->assertNotNull($outbox);
        $this->assertSame(MoodleOutboxService::ACTION_ASSIGN, $outbox->action);
        $this->assertSame('primary', $outbox->payload['role']);
        $this->assertSame($ctx['tenant']->id, $outbox->tenant_id);
        $this->assertSame((int) config('moodle.role_map.teacher', 3), CourseOfferingLecturerRole::Primary->moodleRoleId());
    }

    public function test_assistant_lecturer_assignment_uses_non_editing_teacher_role(): void
    {
        $ctx = $this->seedContext();

        CourseOfferingLecturer::create([
            'tenant_id' => $ctx['tenant']->id,
            'course_offering_id' => $ctx['offering']->id,
            'lecturer_id' => $ctx['lecturer']->id,
            'role' => CourseOfferingLecturerRole::Assistant,
            'is_active' => true,
        ]);

        $this->assertSame(
            (int) config('moodle.role_map.assistant_teacher', 4),
            CourseOfferingLecturerRole::Assistant->moodleRoleId()
        );
    }

    public function test_deleting_assignment_enqueues_unassign(): void
    {
        $ctx = $this->seedContext();

        $assignment = CourseOfferingLecturer::create([
            'tenant_id' => $ctx['tenant']->id,
            'course_offering_id' => $ctx['offering']->id,
            'lecturer_id' => $ctx['lecturer']->id,
            'role' => CourseOfferingLecturerRole::Primary,
            'is_active' => true,
        ]);

        MoodleSyncOutbox::query()->delete();

        $assignment->delete();

        $outbox = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_LECTURER_ASSIGNMENT)
            ->where('entity_id', $assignment->id)
            ->first();

        $this->assertNotNull($outbox);
        $this->assertSame(MoodleOutboxService::ACTION_UNASSIGN, $outbox->action);
    }

    public function test_moodle_course_id_resolution_uses_course_mapping(): void
    {
        $ctx = $this->seedContext();

        MoodleEntityMapping::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_COURSE_OFFERING)
            ->where('fos_entity_id', $ctx['offering']->id)
            ->delete();

        MoodleEntityMapping::updateOrCreate(
            [
                'entity_type' => 'course',
                'moodle_idnumber' => 'fos_course_'.$ctx['course']->id,
            ],
            [
                'fos_entity_id' => $ctx['course']->id,
                'tenant_id' => $ctx['tenant']->id,
                'moodle_id' => 9911,
            ],
        );

        $service = app(MoodleSyncService::class);
        $this->assertSame(9911, $service->resolveMoodleCourseIdForOffering($ctx['offering']));
    }

    /**
     * @return array{tenant: Tenant, organization: Organization, user: User, lecturer: Lecturer, course: Course, offering: CourseOffering}
     */
    protected function seedContext(): array
    {
        $rand = Str::random(4);

        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'lect-test'],
            ['name' => 'Lecturer Test', 'included_modules' => ['core', 'campus']],
        );

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "lect-{$rand}",
            'name' => 'Univ Lect',
            'subscription_plan_id' => $plan->id,
        ]);

        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "lect-org-{$rand}",
            'name' => 'Org Lect',
        ]);

        $user = User::create([
            'name' => 'Dosen '.$rand,
            'email' => "dosen-{$rand}@example.com",
            'password' => 'password',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => "y-{$rand}",
            'name' => 'Year',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'academic_year_id' => $year->id,
            'code' => "p-{$rand}",
            'name' => 'Period',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
        ]);

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'code' => "FT-{$rand}",
            'name' => 'Faculty',
        ]);

        $studyProgram = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'faculty_id' => $faculty->id,
            'code' => "SI-{$rand}",
            'name' => 'Information Systems',
            'total_credits_required' => 144,
        ]);

        $lecturer = Lecturer::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'study_program_id' => $studyProgram->id,
            'nidn' => "NIDN-{$rand}",
            'full_name' => 'Dosen '.$rand,
            'is_active' => true,
        ]);

        $course = Course::create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $studyProgram->id,
            'code' => "IF-{$rand}",
            'name' => 'Algorithms',
            'credits' => 3,
        ]);

        $offering = CourseOffering::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'course_id' => $course->id,
            'academic_period_id' => $period->id,
            'class_code' => 'A',
            'capacity' => 30,
        ]);

        return compact('tenant', 'org', 'user', 'lecturer', 'course', 'offering') + [
            'organization' => $org,
        ];
    }
}
