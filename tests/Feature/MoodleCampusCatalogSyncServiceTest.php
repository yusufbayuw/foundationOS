<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleCampusCatalogSyncService;
use App\Models\MoodleOfferingMapping;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

class MoodleCampusCatalogSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_course_template_mapping_uses_canonical_idnumber(): void
    {
        $ctx = $this->seedCatalogue();
        $course = $ctx['course'];

        $mapping = app(MoodleCampusCatalogSyncService::class)->syncCourseTemplate($course);

        $this->assertSame(MoodleOfferingMapping::KIND_TEMPLATE, $mapping->kind);
        $this->assertSame("fos_template_{$course->tenant_id}_{$course->id}", $mapping->idnumber);
        $this->assertSame((int) $course->id, (int) $mapping->course_id);
        $this->assertNull($mapping->course_offering_id);

        $this->assertSame(1, MoodleSyncOutbox::query()
            ->where('dedupe_key', "campus:template:{$course->tenant_id}:{$course->id}")
            ->count());
    }

    public function test_offering_mapping_uses_fos_offering_idnumber_and_links_template(): void
    {
        $ctx = $this->seedCatalogue();

        $mapping = app(MoodleCampusCatalogSyncService::class)->syncCourseOffering($ctx['offering']);

        $this->assertSame(MoodleOfferingMapping::KIND_OFFERING, $mapping->kind);
        $this->assertSame("fos_offering_{$ctx['offering']->id}", $mapping->idnumber);

        $outbox = MoodleSyncOutbox::query()
            ->where('dedupe_key', "campus:offering:{$ctx['offering']->id}")
            ->first();

        $this->assertNotNull($outbox);
        $payload = $outbox->payload;
        $this->assertSame(
            "fos_template_{$ctx['tenant']->id}_{$ctx['course']->id}",
            $payload['template_idnumber'],
        );
        $this->assertSame($ctx['offering']->class_code, $payload['class_code']);
    }

    public function test_offering_payload_includes_prerequisite_restrictions(): void
    {
        $ctx = $this->seedCatalogue();

        $prereqCourse = Course::query()->create([
            'tenant_id' => $ctx['tenant']->id,
            'study_program_id' => $ctx['program']->id,
            'code' => 'PRE-101',
            'name' => 'Prereq 101',
            'credits' => 3,
            'semester_level' => 1,
            'is_active' => true,
        ]);

        CoursePrerequisite::query()->create([
            'tenant_id' => $ctx['tenant']->id,
            'course_id' => $ctx['course']->id,
            'prerequisite_course_id' => $prereqCourse->id,
            'min_grade' => 70.00,
            'is_strict' => true,
        ]);

        app(MoodleCampusCatalogSyncService::class)->syncCourseOffering($ctx['offering']);

        $outbox = MoodleSyncOutbox::query()
            ->where('dedupe_key', "campus:offering:{$ctx['offering']->id}")
            ->first();

        $restrictions = $outbox->payload['restrictions'] ?? [];
        $this->assertCount(1, $restrictions);
        $this->assertSame(
            "fos_template_{$ctx['tenant']->id}_{$prereqCourse->id}",
            $restrictions[0]['prerequisite_idnumber'],
        );
        $this->assertEqualsWithDelta(70.0, (float) $restrictions[0]['min_grade'], 0.01);
        $this->assertTrue($restrictions[0]['is_strict']);
    }

    public function test_sync_is_idempotent_via_unique_idnumber(): void
    {
        $ctx = $this->seedCatalogue();
        $svc = app(MoodleCampusCatalogSyncService::class);

        $svc->syncCourseTemplate($ctx['course']);
        $svc->syncCourseTemplate($ctx['course']);
        $svc->syncCourseOffering($ctx['offering']);
        $svc->syncCourseOffering($ctx['offering']);

        $this->assertSame(1, MoodleOfferingMapping::query()
            ->where('idnumber', "fos_template_{$ctx['tenant']->id}_{$ctx['course']->id}")
            ->count());
        $this->assertSame(1, MoodleOfferingMapping::query()
            ->where('idnumber', "fos_offering_{$ctx['offering']->id}")
            ->count());
    }

    protected function seedCatalogue(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'campus-sync-test'],
            ['name' => 'Campus Sync', 'included_modules' => ['core', 'campus']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "cs-{$rand}",
            'name' => 'Campus Sync Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "cs-org-{$rand}",
            'name' => 'Campus Org',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'code' => "cy-{$rand}",
            'name' => 'Year',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'is_active' => true,
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'code' => "cp-{$rand}",
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

        $course = Course::query()->create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $program->id,
            'code' => "CRS-{$rand}",
            'name' => 'Calculus I',
            'credits' => 3,
            'semester_level' => 1,
            'is_active' => true,
        ]);

        $offering = CourseOffering::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'course_id' => $course->id,
            'academic_period_id' => $period->id,
            'class_code' => "A-{$rand}",
            'capacity' => 30,
            'delivery_mode' => 'offline',
            'status' => 'active',
        ]);

        return compact('tenant', 'org', 'period', 'program', 'course', 'offering');
    }
}
