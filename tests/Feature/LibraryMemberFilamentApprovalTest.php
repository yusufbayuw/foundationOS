<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Modules\Library\Filament\Resources\Members\Pages\ListMembers;
use Modules\Library\Models\Member;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class LibraryMemberFilamentApprovalTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_pending_member_can_be_approved_from_table_action(): void
    {
        ['tenant' => $tenant, 'user' => $admin] = $this->bootstrapFilament();
        $memberUser = User::factory()->create();
        $member = $this->createPendingMember($tenant->id, $memberUser->id);

        Livewire::test(ListMembers::class)
            ->callAction(TestAction::make('approve')->table($member))
            ->assertNotified();

        $member->refresh();

        $this->assertSame('approved', $member->status);
        $this->assertNotNull($member->verified_at);
        $this->assertSame($admin->id, $member->verified_by);
        $this->assertNull($member->rejection_reason);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => $memberUser->getMorphClass(),
            'notifiable_id' => $memberUser->id,
        ]);
    }

    public function test_pending_member_can_be_rejected_with_reason_from_table_action(): void
    {
        ['tenant' => $tenant] = $this->bootstrapFilament();
        $memberUser = User::factory()->create();
        $member = $this->createPendingMember($tenant->id, $memberUser->id);

        Livewire::test(ListMembers::class)
            ->callAction(TestAction::make('reject')->table($member), [
                'rejection_reason' => 'Dokumen bukti tidak valid.',
            ])
            ->assertNotified();

        $member->refresh();

        $this->assertSame('rejected', $member->status);
        $this->assertSame('Dokumen bukti tidak valid.', $member->rejection_reason);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => $memberUser->getMorphClass(),
            'notifiable_id' => $memberUser->id,
        ]);
    }

    public function test_reject_action_requires_rejection_reason(): void
    {
        ['tenant' => $tenant] = $this->bootstrapFilament();
        $memberUser = User::factory()->create();
        $member = $this->createPendingMember($tenant->id, $memberUser->id);

        Livewire::test(ListMembers::class)
            ->callAction(TestAction::make('reject')->table($member), [
                'rejection_reason' => null,
            ])
            ->assertHasActionErrors(['rejection_reason' => 'required']);

        $this->assertSame('pending', $member->refresh()->status);
    }

    /**
     * @return array{tenant: Tenant, user: User}
     */
    private function bootstrapFilament(): array
    {
        $context = $this->makeTenantContext(['core', 'library']);
        $superAdmin = User::factory()->superAdmin()->create();

        UserTenantRole::create([
            'user_id' => $superAdmin->id,
            'tenant_id' => $context['tenant']->id,
            'organization_id' => $context['organization']->id,
            'tenant_role_id' => $context['role']->id,
            'assigned_by' => $superAdmin->id,
            'is_primary' => true,
        ]);

        app(ApplicationModuleCatalog::class)->sync();
        app(TenantModuleProvisioner::class)->enableForTenant($context['tenant'], ['core', 'library']);

        TenantModule::query()
            ->where('tenant_id', $context['tenant']->id)
            ->update(['is_enabled' => true]);

        $this->actingAs($superAdmin);
        app(CurrentTenant::class)->set($context['tenant']);
        Filament::setTenant($context['tenant']);
        Filament::setCurrentPanel('admin');

        return ['tenant' => $context['tenant'], 'user' => $superAdmin];
    }

    private function createPendingMember(int $tenantId, int $userId): Member
    {
        return Member::factory()->create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'rejection_reason' => null,
        ]);
    }
}
