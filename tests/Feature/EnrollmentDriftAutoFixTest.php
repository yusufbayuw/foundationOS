<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleEnrollmentDriftFixer;
use App\Models\MoodleEnrollmentDrift;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Tests\TestCase;

class EnrollmentDriftAutoFixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('moodle.enabled', true);
    }

    public function test_missing_in_moodle_drift_enqueues_outbox_enrol(): void
    {
        $ctx = $this->seedContext(mode: MoodleEnrollmentDriftFixer::MODE_OUTBOUND_ONLY);

        $drift = $this->makeDrift($ctx, MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE, [
            'user_id' => $ctx['user']->id,
            'user_moodle_idnumber' => 'fos_user_'.$ctx['user']->id,
            'expected_role' => 'student',
        ]);

        $this->assertTrue(app(MoodleEnrollmentDriftFixer::class)->fix($drift));

        $this->assertNotNull($drift->fresh()->resolved_at);
        $this->assertSame(MoodleEnrollmentDriftFixer::RESOLUTION_OUTBOX_ENROL, $drift->fresh()->resolution);
        $this->assertSame(1, MoodleSyncOutbox::query()
            ->where('action', 'enroll')
            ->where('tenant_id', $ctx['tenant']->id)
            ->count());
    }

    public function test_mismatched_role_drift_enqueues_reassign(): void
    {
        $ctx = $this->seedContext();
        $drift = $this->makeDrift($ctx, MoodleEnrollmentDrift::TYPE_MISMATCHED_ROLE, [
            'user_id' => $ctx['user']->id,
            'user_moodle_idnumber' => 'fos_user_'.$ctx['user']->id,
            'expected_role' => 'student',
            'actual_role' => 'teacher',
        ]);

        app(MoodleEnrollmentDriftFixer::class)->fix($drift);

        $this->assertSame(MoodleEnrollmentDriftFixer::RESOLUTION_OUTBOX_REASSIGN, $drift->fresh()->resolution);
    }

    public function test_missing_in_fos_with_outbound_only_marks_for_review(): void
    {
        $ctx = $this->seedContext(mode: MoodleEnrollmentDriftFixer::MODE_OUTBOUND_ONLY);
        $drift = $this->makeDrift($ctx, MoodleEnrollmentDrift::TYPE_MISSING_IN_FOS, [
            'user_id' => 9999,
            'user_moodle_idnumber' => 'fos_user_9999',
        ]);

        app(MoodleEnrollmentDriftFixer::class)->fix($drift);

        $this->assertNull($drift->fresh()->resolved_at, 'must NOT auto-resolve inbound drift in outbound_only mode');
        $this->assertSame(MoodleEnrollmentDriftFixer::RESOLUTION_MARKED_FOR_REVIEW, $drift->fresh()->resolution);
        $this->assertSame(0, ClassStudent::withoutTenantScope()->count(), 'no class_student row created');
    }

    public function test_missing_in_fos_with_bidirectional_creates_class_student(): void
    {
        $ctx = $this->seedContext(mode: MoodleEnrollmentDriftFixer::MODE_BIDIRECTIONAL);
        $drift = $this->makeDrift($ctx, MoodleEnrollmentDrift::TYPE_MISSING_IN_FOS, [
            'user_id' => $ctx['user']->id,
            'user_moodle_idnumber' => 'fos_user_'.$ctx['user']->id,
        ]);

        app(MoodleEnrollmentDriftFixer::class)->fix($drift);

        $this->assertSame(MoodleEnrollmentDriftFixer::RESOLUTION_FOS_CLASS_STUDENT_CREATED, $drift->fresh()->resolution);
        $this->assertSame(1, ClassStudent::withoutTenantScope()
            ->where('tenant_id', $ctx['tenant']->id)
            ->where('class_id', $ctx['class']->id)
            ->where('student_id', $ctx['student']->id)
            ->count());
    }

    public function test_disabled_mode_marks_everything_for_review(): void
    {
        $ctx = $this->seedContext(mode: MoodleEnrollmentDriftFixer::MODE_DISABLED);
        $drift = $this->makeDrift($ctx, MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE, [
            'user_id' => $ctx['user']->id,
        ]);

        app(MoodleEnrollmentDriftFixer::class)->fix($drift);

        $this->assertSame(MoodleEnrollmentDriftFixer::RESOLUTION_MARKED_FOR_REVIEW, $drift->fresh()->resolution);
        $this->assertSame(
            0,
            MoodleSyncOutbox::query()
                ->where('tenant_id', $ctx['tenant']->id)
                ->where('action', 'enroll')
                ->count(),
        );
    }

    public function test_already_resolved_drift_is_skipped(): void
    {
        $ctx = $this->seedContext();
        $drift = $this->makeDrift($ctx, MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE, [
            'user_id' => $ctx['user']->id,
            'resolved_at' => now(),
            'resolution' => 'manual_resolve',
        ]);

        $this->assertFalse(app(MoodleEnrollmentDriftFixer::class)->fix($drift));
        $this->assertSame('manual_resolve', $drift->fresh()->resolution);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function makeDrift(array $ctx, string $type, array $overrides = []): MoodleEnrollmentDrift
    {
        return MoodleEnrollmentDrift::query()->create(array_merge([
            'tenant_id' => $ctx['tenant']->id,
            'class_id' => $ctx['class']->id,
            'course_moodle_id' => 5500,
            'drift_type' => $type,
            'detected_at' => now(),
        ], $overrides));
    }

    protected function seedContext(string $mode = MoodleEnrollmentDriftFixer::MODE_OUTBOUND_ONLY): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'drift-fix-test'],
            ['name' => 'Drift Fix', 'included_modules' => ['core', 'school']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "dfix-{$rand}",
            'name' => 'Drift Fix',
            'subscription_plan_id' => $plan->id,
        ]);

        TenantSetting::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'group' => MoodleEnrollmentDriftFixer::SETTING_GROUP,
            'key' => MoodleEnrollmentDriftFixer::SETTING_KEY,
            'value' => $mode,
            'type' => 'string',
        ]);

        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "dfix-org-{$rand}",
            'name' => 'Org',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'code' => "y-{$rand}",
            'name' => 'Year',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'is_active' => true,
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'code' => "p-{$rand}",
            'name' => 'Period',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'academic_period_id' => $period->id,
            'name' => 'Class',
            'code' => "C-{$rand}",
            'grade_level' => 10,
        ]);

        $user = User::create([
            'name' => 'Drift User',
            'email' => "drift-{$rand}@example.com",
            'password' => 'password',
        ]);

        $student = Student::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'nis' => "NIS-{$rand}",
            'entry_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        return compact('tenant', 'org', 'period', 'class', 'user', 'student');
    }
}
