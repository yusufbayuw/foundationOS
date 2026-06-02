<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleEnrollmentReconciler;
use App\Models\MoodleClassCourseMapping;
use App\Models\MoodleEnrollmentDrift;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Tests\TestCase;

class EnrollmentDriftDetectorTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_three_drift_types_detected_from_seeded_mismatches(): void
    {
        $ctx = $this->seedMappingWithMixedEnrollment();

        $drifts = app(MoodleEnrollmentReconciler::class)
            ->reconcileMapping($ctx['mapping'], $ctx['moodle_enrollment']);

        $this->assertCount(3, $drifts);

        $byType = $drifts->groupBy('drift_type');
        $this->assertCount(1, $byType[MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE]);
        $this->assertCount(1, $byType[MoodleEnrollmentDrift::TYPE_MISSING_IN_FOS]);
        $this->assertCount(1, $byType[MoodleEnrollmentDrift::TYPE_MISMATCHED_ROLE]);

        // Persisted to DB.
        $this->assertSame(3, MoodleEnrollmentDrift::withoutTenantScope()
            ->where('tenant_id', $ctx['tenant']->id)
            ->count());

        // Missing-in-Moodle should reference the FOS user_id.
        $this->assertSame(
            $ctx['user_only_in_fos']->id,
            (int) $byType[MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE]->first()->user_id,
        );

        // Mismatched role records actual_role from Moodle payload.
        $mismatch = $byType[MoodleEnrollmentDrift::TYPE_MISMATCHED_ROLE]->first();
        $this->assertSame('teacher', $mismatch->actual_role);
        $this->assertSame('student', $mismatch->expected_role);
    }

    public function test_dry_run_does_not_persist_any_drift_rows(): void
    {
        $ctx = $this->seedMappingWithMixedEnrollment();

        $drifts = app(MoodleEnrollmentReconciler::class)
            ->reconcileMapping($ctx['mapping'], $ctx['moodle_enrollment'], dryRun: true);

        $this->assertCount(3, $drifts);
        $this->assertSame(0, MoodleEnrollmentDrift::withoutTenantScope()->count());
    }

    public function test_non_fos_managed_users_in_moodle_are_ignored(): void
    {
        $ctx = $this->seedMappingWithMixedEnrollment();

        // Add a Moodle user without the fos_user_ prefix (e.g. a manually
        // added external account); should NOT produce a drift.
        $ctx['moodle_enrollment'][] = [
            'id' => 9999,
            'idnumber' => 'external_admin_42',
            'roles' => [['shortname' => 'teacher']],
        ];

        $drifts = app(MoodleEnrollmentReconciler::class)
            ->reconcileMapping($ctx['mapping'], $ctx['moodle_enrollment']);

        $this->assertCount(3, $drifts, 'external accounts must be skipped');
    }

    /**
     * Seeds a class with 3 FOS students. Builds a Moodle enrollment payload
     * such that:
     *   - student A is present in both with correct role  → no drift
     *   - student B is in FOS only                       → missing_in_moodle
     *   - student C is in Moodle only (idnumber fos_user_<id>) → missing_in_fos
     *   - student D is in both but Moodle role is `teacher` → mismatched_role
     */
    protected function seedMappingWithMixedEnrollment(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'drift-test'],
            ['name' => 'Drift Test', 'included_modules' => ['core', 'school']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "drift-{$rand}",
            'name' => 'Drift Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $org = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "drift-org-{$rand}",
            'name' => 'Drift Org',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'code' => "drift-year-{$rand}",
            'name' => 'Drift Year',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'is_active' => true,
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'code' => "drift-period-{$rand}",
            'name' => 'Drift Period',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $org->id,
            'academic_period_id' => $period->id,
            'name' => 'Class X',
            'code' => "X-{$rand}",
            'grade_level' => 10,
        ]);

        $users = [];
        $students = [];
        foreach (['A', 'B', 'D'] as $letter) {
            $users[$letter] = User::create([
                'name' => "User {$letter}",
                'email' => strtolower("user-{$letter}-{$rand}@example.com"),
                'password' => 'password',
            ]);
            $students[$letter] = Student::create([
                'tenant_id' => $tenant->id,
                'organization_id' => $org->id,
                'user_id' => $users[$letter]->id,
                'nis' => "NIS-{$letter}-{$rand}",
                'entry_date' => now()->toDateString(),
                'status' => 'active',
            ]);
            ClassStudent::create([
                'tenant_id' => $tenant->id,
                'academic_period_id' => $period->id,
                'class_id' => $class->id,
                'student_id' => $students[$letter]->id,
                'status' => 'active',
                'entry_date' => now()->toDateString(),
            ]);
        }

        // Student C exists only in Moodle (no FOS user → user_only_in_moodle).
        $userOnlyInMoodleId = 99001;

        $mapping = MoodleClassCourseMapping::create([
            'tenant_id' => $tenant->id,
            'class_id' => $class->id,
            'course_id' => 0,
            'moodle_course_id' => 5500,
            'moodle_course_idnumber' => "fos_class_{$class->id}",
            'is_active' => true,
        ]);

        $moodleEnrollment = [
            // A: matched, no drift
            [
                'id' => 7001,
                'idnumber' => MoodleEnrollmentReconciler::FOS_USER_PREFIX.$users['A']->id,
                'roles' => [['shortname' => 'student']],
            ],
            // (B missing in Moodle → drift)
            // C: in Moodle only → missing_in_fos
            [
                'id' => 7002,
                'idnumber' => MoodleEnrollmentReconciler::FOS_USER_PREFIX.$userOnlyInMoodleId,
                'roles' => [['shortname' => 'student']],
            ],
            // D: in both but wrong role
            [
                'id' => 7003,
                'idnumber' => MoodleEnrollmentReconciler::FOS_USER_PREFIX.$users['D']->id,
                'roles' => [['shortname' => 'teacher']],
            ],
        ];

        return [
            'tenant' => $tenant,
            'mapping' => $mapping,
            'moodle_enrollment' => $moodleEnrollment,
            'user_only_in_fos' => $users['B'],
        ];
    }
}
