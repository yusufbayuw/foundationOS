<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Alumni\Models\JobApplication;
use Modules\Alumni\Policies\JobApplicationPolicy;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Member\Models\Member;
use Modules\Member\Models\MemberProfile;
use Modules\Member\Models\MemberProof;
use Modules\Member\Models\MemberType;
use Modules\Member\Policies\MemberPolicy;
use Modules\Member\Policies\MemberProfilePolicy;
use Modules\Member\Policies\MemberProofPolicy;
use Modules\Member\Policies\MemberTypePolicy;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class MemberReviewWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_reviewer_can_approve_a_pending_member_with_an_audited_transition(): void
    {
        [$member, $reviewer] = $this->makePendingMember('MBR-APPROVE');

        $member->approve($reviewer);

        $member->refresh();

        $this->assertSame('approved', $member->status);
        $this->assertTrue($member->verified_at?->isToday() ?? false);
        $this->assertSame($reviewer->getKey(), $member->verified_by);
        $this->assertNull($member->rejection_reason);

        $activity = Activity::query()->forSubject($member)->latest('id')->first();

        $this->assertNotNull($activity);
        $this->assertSame('updated', $activity->event);
        $this->assertSame('approved', $activity->attribute_changes->get('attributes')['status'] ?? null);
    }

    public function test_reviewer_can_reject_a_member_with_a_reason_and_audited_transition(): void
    {
        [$member, $reviewer] = $this->makePendingMember('MBR-REJECT');

        $member->reject($reviewer, 'Identity document is unreadable.');

        $member->refresh();

        $this->assertSame('rejected', $member->status);
        $this->assertSame($reviewer->getKey(), $member->verified_by);
        $this->assertSame('Identity document is unreadable.', $member->rejection_reason);

        $activity = Activity::query()->forSubject($member)->latest('id')->first();

        $this->assertNotNull($activity);
        $this->assertSame('rejected', $activity->attribute_changes->get('attributes')['status'] ?? null);
        $this->assertSame(
            'Identity document is unreadable.',
            $activity->attribute_changes->get('attributes')['rejection_reason'] ?? null,
        );
    }

    public function test_new_filament_models_resolve_to_explicit_shield_policies(): void
    {
        $this->assertInstanceOf(JobApplicationPolicy::class, Gate::getPolicyFor(JobApplication::class));
        $this->assertInstanceOf(MemberPolicy::class, Gate::getPolicyFor(Member::class));
        $this->assertInstanceOf(MemberTypePolicy::class, Gate::getPolicyFor(MemberType::class));
        $this->assertInstanceOf(MemberProfilePolicy::class, Gate::getPolicyFor(MemberProfile::class));
        $this->assertInstanceOf(MemberProofPolicy::class, Gate::getPolicyFor(MemberProof::class));
    }

    /**
     * @return array{Member, User}
     */
    private function makePendingMember(string $memberNumber): array
    {
        $tenant = Tenant::factory()->create();
        $memberUser = User::factory()->create();
        $reviewer = User::factory()->create();
        $memberType = MemberType::query()->create([
            'code' => strtolower($memberNumber),
            'name' => 'Test membership',
        ]);

        app(CurrentTenant::class)->set($tenant);

        $member = Member::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $memberUser->getKey(),
            'member_number' => $memberNumber,
            'domain_member_type_id' => $memberType->getKey(),
            'status' => 'pending',
        ]);

        return [$member, $reviewer];
    }
}
