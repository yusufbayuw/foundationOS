<?php

namespace Tests\Feature;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Models\Announcement;
use Modules\Core\Models\Broadcast;
use Modules\Core\Models\ParentStudent;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Services\BroadcastService;
use Modules\Counseling\Models\CounselingNote;
use Modules\Counseling\Policies\CounselingNotePolicy;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\Violation;
use Modules\School\Services\StudentRiskScoreService;
use Tests\TestCase;

class RoadmapV06AcademicExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_panel_isolates_children_between_parents(): void
    {
        $tenant = $this->makeTenant();

        $parentA = User::factory()->create();
        $parentB = User::factory()->create();

        $studentA = $this->makeStudent($tenant, 'Student A');
        $studentB = $this->makeStudent($tenant, 'Student B');

        ParentStudent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'parent_user_id' => $parentA->getKey(),
            'student_id' => $studentA->getKey(),
            'relationship' => 'father',
            'is_primary' => true,
        ]);

        ParentStudent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'parent_user_id' => $parentB->getKey(),
            'student_id' => $studentB->getKey(),
            'relationship' => 'mother',
            'is_primary' => true,
        ]);

        $visibleToA = Student::query()
            ->whereIn('id', ParentStudent::query()
                ->where('parent_user_id', $parentA->getKey())
                ->pluck('student_id'))
            ->pluck('id');

        $this->assertTrue($visibleToA->contains($studentA->getKey()));
        $this->assertFalse($visibleToA->contains($studentB->getKey()));
    }

    public function test_confidential_counseling_note_policy_restricts_view(): void
    {
        $tenant = $this->makeTenant();
        $staff = User::factory()->create();
        $superAdmin = User::factory()->create(['is_super_admin' => true]);

        $note = CounselingNote::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Confidential session',
            'status' => 'active',
            'is_confidential' => true,
            'body' => 'Sensitive content',
        ]);

        $policy = new CounselingNotePolicy;

        $this->assertFalse($policy->view($staff, $note));
        $this->assertTrue($policy->view($superAdmin, $note));
    }

    public function test_broadcast_service_uses_idempotency_key(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();

        $broadcast = Broadcast::query()->create([
            'tenant_id' => $tenant->getKey(),
            'subject' => 'School update',
            'body' => 'Hello parents',
            'channels' => ['database'],
            'status' => 'draft',
        ]);

        $service = app(BroadcastService::class);
        $users = Collection::make([$user]);

        $service->send($broadcast, $users);
        $service->send($broadcast->fresh(), $users);

        $this->assertSame(
            1,
            NotificationDelivery::query()
                ->where('idempotency_key', "broadcast:{$broadcast->getKey()}:user:{$user->getKey()}")
                ->count(),
        );
    }

    public function test_student_risk_score_recompute_is_deterministic(): void
    {
        $tenant = $this->makeTenant();
        $student = $this->makeStudent($tenant, 'Risk Student');

        Attendance::query()->create([
            'tenant_id' => $tenant->getKey(),
            'student_id' => $student->getKey(),
            'attendance_date' => now()->toDateString(),
            'status' => 'absent',
        ]);

        Violation::query()->create([
            'tenant_id' => $tenant->getKey(),
            'student_id' => $student->getKey(),
            'description' => 'Late to class',
            'status' => 'open',
        ]);

        $service = app(StudentRiskScoreService::class);
        $first = $service->recomputeAndStore($student);
        $second = $service->recomputeAndStore($student->fresh());

        $this->assertSame($first->composite_score, $second->composite_score);
        $this->assertSame($first->academic_score, $second->academic_score);
        $this->assertSame($first->attendance_score, $second->attendance_score);
    }

    public function test_v06_scheduled_commands_are_registered(): void
    {
        $this->artisan('school:recompute-risk-scores')->assertSuccessful();
        $this->artisan('counseling:scan-risk')->assertSuccessful();
        $this->artisan('comms:send-newsletter')->assertSuccessful();
        $this->artisan('alumni:tracer-study-blast')->assertSuccessful();
    }

    public function test_parent_can_access_panel_when_linked_to_student(): void
    {
        $tenant = $this->makeTenant();
        $parent = User::factory()->create();
        $student = $this->makeStudent($tenant, 'Child');

        ParentStudent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'parent_user_id' => $parent->getKey(),
            'student_id' => $student->getKey(),
            'relationship' => 'guardian',
            'is_primary' => true,
        ]);

        $panel = Filament::getPanel('parent');
        $this->assertTrue($parent->canAccessPanel($panel));
    }

    public function test_announcement_model_persists_for_parent_feed(): void
    {
        $tenant = $this->makeTenant();

        $announcement = Announcement::query()->create([
            'tenant_id' => $tenant->getKey(),
            'title' => 'Holiday notice',
            'body' => 'School closed tomorrow',
            'audience' => 'parent',
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->getKey(),
            'title' => 'Holiday notice',
        ]);
    }

    protected function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'starter-'.Str::random(4),
            'name' => 'Starter',
            'included_modules' => ['core', 'school'],
        ]);

        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-'.Str::random(6),
            'name' => 'Test Tenant',
            'subscription_plan_id' => $plan->getKey(),
        ]);
    }

    protected function makeStudent(Tenant $tenant, string $name): Student
    {
        $user = User::factory()->create(['name' => $name]);

        return Student::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getKey(),
            'status' => 'active',
        ]);
    }
}
