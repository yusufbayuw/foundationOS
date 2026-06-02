<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleMapper;
use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncService;
use App\Models\MoodleEntityMapping;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\CoursePrerequisite;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Tests\TestCase;

class CourseOfferingSyncsToMoodleTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_creating_offering_enqueues_upsert_outbox(): void
    {
        $ctx = $this->seedContext();
        $offering = $this->makeOffering($ctx);

        $outbox = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_COURSE_OFFERING)
            ->where('entity_id', $offering->id)
            ->first();

        $this->assertNotNull($outbox);
        $this->assertSame(MoodleOutboxService::ACTION_UPSERT, $outbox->action);
        $this->assertSame($ctx['tenant']->id, $outbox->tenant_id);
        $this->assertSame($ctx['course']->id, $outbox->payload['course_id']);
    }

    public function test_cancelling_offering_enqueues_deactivate(): void
    {
        $ctx = $this->seedContext();
        $offering = $this->makeOffering($ctx);
        MoodleSyncOutbox::query()->delete();

        $offering->update(['status' => 'cancelled']);

        $outbox = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_COURSE_OFFERING)
            ->where('entity_id', $offering->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($outbox);
        $this->assertSame(MoodleOutboxService::ACTION_DEACTIVATE, $outbox->action);
    }

    public function test_idnumber_convention_is_fos_offering_id(): void
    {
        $mapper = app(MoodleMapper::class);
        $this->assertSame('fos_offering_42', $mapper->courseOfferingIdnumber(42));
        $this->assertSame('fos_semester_t1_p7', $mapper->semesterCategoryIdnumber(1, 7));
        $this->assertSame('fos_template_3', $mapper->courseTemplateCategoryIdnumber(3));
    }

    public function test_offering_mapping_takes_precedence_in_resolver(): void
    {
        $ctx = $this->seedContext();
        $offering = $this->makeOffering($ctx);

        MoodleEntityMapping::updateOrCreate(
            [
                'entity_type' => 'course',
                'moodle_idnumber' => 'fos_course_'.$ctx['course']->id,
            ],
            [
                'fos_entity_id' => $ctx['course']->id,
                'tenant_id' => $ctx['tenant']->id,
                'moodle_id' => 1000,
            ],
        );

        MoodleEntityMapping::updateOrCreate(
            [
                'entity_type' => MoodleOutboxService::ENTITY_COURSE_OFFERING,
                'moodle_idnumber' => 'fos_offering_'.$offering->id,
            ],
            [
                'fos_entity_id' => $offering->id,
                'tenant_id' => $ctx['tenant']->id,
                'moodle_id' => 2000,
            ],
        );

        $service = app(MoodleSyncService::class);
        $this->assertSame(2000, $service->resolveMoodleCourseIdForOffering($offering));
    }

    public function test_resolver_falls_back_to_course_mapping_when_no_offering_mapping(): void
    {
        $ctx = $this->seedContext();
        $offering = $this->makeOffering($ctx);

        MoodleEntityMapping::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_COURSE_OFFERING)
            ->where('fos_entity_id', $offering->id)
            ->delete();

        MoodleEntityMapping::updateOrCreate(
            [
                'entity_type' => 'course',
                'moodle_idnumber' => 'fos_course_'.$ctx['course']->id,
            ],
            [
                'fos_entity_id' => $ctx['course']->id,
                'tenant_id' => $ctx['tenant']->id,
                'moodle_id' => 1000,
            ],
        );

        $service = app(MoodleSyncService::class);
        $this->assertSame(1000, $service->resolveMoodleCourseIdForOffering($offering));
    }

    public function test_mapper_includes_prerequisite_hint_in_summary(): void
    {
        $ctx = $this->seedContext();
        $offering = $this->makeOffering($ctx);

        $prereq = Course::create([
            'tenant_id' => $ctx['tenant']->id,
            'study_program_id' => $ctx['studyProgram']->id,
            'code' => 'MA101',
            'name' => 'Calculus I',
            'credits' => 3,
        ]);

        CoursePrerequisite::create([
            'tenant_id' => $ctx['tenant']->id,
            'course_id' => $ctx['course']->id,
            'prerequisite_course_id' => $prereq->id,
            'min_grade' => 2.0,
            'is_required' => true,
        ]);

        $service = app(MoodleSyncService::class);
        $reflection = new \ReflectionMethod($service, 'buildPrerequisiteHint');
        $reflection->setAccessible(true);
        $hint = $reflection->invoke($service, $offering->fresh(['course']));

        $this->assertStringContainsString('Prerequisites', $hint);
        $this->assertStringContainsString('MA101', $hint);
        $this->assertStringContainsString('min grade 2.00', $hint);
    }

    protected function makeOffering(array $ctx): CourseOffering
    {
        return CourseOffering::create([
            'tenant_id' => $ctx['tenant']->id,
            'organization_id' => $ctx['organization']->id,
            'course_id' => $ctx['course']->id,
            'academic_period_id' => $ctx['period']->id,
            'class_code' => 'A',
            'capacity' => 30,
            'status' => 'open',
        ]);
    }

    protected function seedContext(): array
    {
        $rand = Str::random(4);

        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'offering-sync-test'],
            ['name' => 'Offering Sync', 'included_modules' => ['core', 'campus']],
        );

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "off-{$rand}",
            'name' => 'Univ Offering',
            'subscription_plan_id' => $plan->id,
        ]);

        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "off-org-{$rand}",
            'name' => 'Org Offering',
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
            'code' => "2026-GANJIL-{$rand}",
            'name' => 'Ganjil',
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

        $course = Course::create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $studyProgram->id,
            'code' => "IF-{$rand}",
            'name' => 'Algorithms',
            'credits' => 3,
        ]);

        return [
            'tenant' => $tenant,
            'organization' => $org,
            'year' => $year,
            'period' => $period,
            'faculty' => $faculty,
            'studyProgram' => $studyProgram,
            'course' => $course,
        ];
    }
}
