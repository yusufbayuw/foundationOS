<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Models\MoodleSyncOutbox;
use App\Observers\AcademicPeriodObserver;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Tests\TestCase;

class AcademicPeriodObserverTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_period_update_enqueues_outbox_for_each_related_offering(): void
    {
        $ctx = $this->seedOffering();

        $ctx['period']->forceFill(['name' => 'Semester Genap 2026'])->save();

        $rows = MoodleSyncOutbox::query()
            ->where('entity_type', MoodleOutboxService::ENTITY_COURSE_OFFERING)
            ->where('entity_id', $ctx['offering']->id)
            ->get();

        $this->assertGreaterThanOrEqual(1, $rows->count());

        $latest = $rows->sortByDesc('id')->first();

        $this->assertSame(MoodleOutboxService::ACTION_UPSERT, $latest->action);
        $this->assertNotEmpty($latest->dedupe_key);
        $this->assertIsArray($latest->payload);
        $this->assertSame('academic_period_updated', $latest->payload['trigger'] ?? null);
    }

    public function test_observer_does_not_throw_when_offerings_exist(): void
    {
        $ctx = $this->seedOffering();

        $observer = app(AcademicPeriodObserver::class);

        $observer->updated($ctx['period']->fresh());

        $this->assertTrue(true);
    }

    /**
     * @return array{tenant:Tenant,period:AcademicPeriod,offering:CourseOffering}
     */
    protected function seedOffering(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'academic-period-observer'],
            ['name' => 'Academic Period Observer', 'included_modules' => ['core', 'campus']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "ap-{$rand}",
            'name' => "Tenant {$rand}",
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus',
            'code' => "ORG-{$rand}",
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => '2026/2027',
            'code' => '2026-2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester Ganjil',
            'code' => 'GANJIL',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'FTI',
            'code' => 'FTI',
        ]);

        $program = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'faculty_id' => $faculty->id,
            'name' => 'Informatika',
            'code' => 'IF',
        ]);

        $course = Course::create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $program->id,
            'code' => 'IF101',
            'name' => 'Pemrograman',
            'credits' => 3,
        ]);

        $offering = CourseOffering::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'course_id' => $course->id,
            'academic_period_id' => $period->id,
            'class_code' => 'A',
            'status' => 'open',
        ]);

        return [
            'tenant' => $tenant,
            'period' => $period,
            'offering' => $offering,
        ];
    }
}
