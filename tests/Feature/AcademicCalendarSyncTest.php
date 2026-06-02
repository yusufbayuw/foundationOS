<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Observers\AcademicPeriodObserver;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class AcademicCalendarSyncTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private AcademicYear $academicYear;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'cal-plan',
            'name' => 'Cal Plan',
            'included_modules' => ['core', 'campus'],
        ]);

        $user = User::create([
            'name' => 'Cal Admin',
            'email' => 'cal-admin@example.com',
            'password' => 'password',
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-cal',
            'name' => 'Cal Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $this->academicYear = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2026/2027',
            'code' => 'AY-2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);
    }

    public function test_academic_period_date_change_triggers_observer_with_moodle_enabled(): void
    {
        config(['moodle.enabled' => true]);

        $outbox = Mockery::mock(MoodleOutboxService::class);
        // No CourseOffering records linked to this period → enqueue is never called.
        // This verifies: observer fires on date change, iterates 0 offerings, no error.
        $outbox->shouldNotReceive('enqueue');

        $period = AcademicPeriod::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Semester Ganjil 2026',
            'code' => 'SG-2026',
            'type' => 'semester',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $observer = new AcademicPeriodObserver($outbox);
        $period->start_date = '2026-09-01';
        $period->syncChanges();

        $observer->updated($period);

        $this->assertTrue(true);
    }

    public function test_academic_period_observer_skips_when_moodle_disabled(): void
    {
        config(['moodle.enabled' => false]);

        $outbox = Mockery::mock(MoodleOutboxService::class);
        $outbox->shouldNotReceive('enqueue');

        $period = AcademicPeriod::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Period No Sync',
            'code' => 'PNS',
            'type' => 'semester',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => false,
        ]);

        $period->start_date = '2026-09-01';
        $period->syncChanges();

        $observer = new AcademicPeriodObserver($outbox);
        $observer->updated($period);

        $this->assertTrue(true);
    }

    public function test_academic_period_observer_skips_irrelevant_field_changes(): void
    {
        config(['moodle.enabled' => true]);

        $outbox = Mockery::mock(MoodleOutboxService::class);
        $outbox->shouldNotReceive('enqueue');

        $period = AcademicPeriod::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Period Skip',
            'code' => 'PSKIP',
            'type' => 'semester',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => false,
            'description' => 'original',
        ]);

        $period->description = 'changed description only';
        $period->syncChanges();

        $observer = new AcademicPeriodObserver($outbox);
        $observer->updated($period);

        $this->assertTrue(true);
    }
}
