<?php

namespace Modules\Workflow\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\PendingCommand;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Workflow\Models\Workflow;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SetupApprovalLimitsCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_seeds_approval_limits_workflow(): void
    {
        [$tenant, $organization, $manager] = $this->makeTenantContext('manager@example.com');
        $direktur = $this->createUserInTenant($tenant, $organization, 'direktur@example.com');
        $komisaris = $this->createUserInTenant($tenant, $organization, 'komisaris@example.com');

        $this->pendingArtisan('fos:workflow:setup-approval-limits', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--direktur' => $direktur->id,
            '--komisaris' => $komisaris->id,
        ])
            ->expectsOutputToContain('Setting up Approval Limit workflows')
            ->expectsOutputToContain('activated.')
            ->assertSuccessful();

        $this->assertDatabaseHas((new Workflow)->getTable(), [
            'tenant_id' => $tenant->id,
            'code' => 'po-approval-limit',
            'subject_type' => PurchaseOrder::class,
            'is_active' => true,
        ]);

        $workflow = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'po-approval-limit')
            ->first();

        $this->assertNotNull($workflow);
        $this->assertCount(4, $workflow->steps);

        $this->assertDatabaseHas('workflow_transitions', [
            'workflow_id' => $workflow->id,
            'action_name' => 'approve',
            'is_default' => false,
        ]);
    }

    #[Test]
    public function it_fails_if_users_do_not_belong_to_tenant(): void
    {
        [$tenant, $organization, $manager] = $this->makeTenantContext('manager@example.com');
        $direktur = $this->createUserInTenant($tenant, $organization, 'direktur@example.com');

        [$otherTenant, $otherOrg, $komisaris] = $this->makeTenantContext('komisaris@other.com');

        $command = $this->pendingArtisan('fos:workflow:setup-approval-limits', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--direktur' => $direktur->id,
            '--komisaris' => $komisaris->id,
        ]);

        $command->assertFailed();
    }

    protected function createUserInTenant(Tenant $tenant, Organization $organization, string $email): User
    {
        $user = User::query()->create([
            'name' => 'User '.$email,
            'email' => $email,
            'password' => 'password',
        ]);

        $tenantRole = TenantRole::query()->firstOrCreate([
            'tenant_id' => $tenant->id,
            'slug' => 'user-role',
        ], [
            'name' => 'User Role',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::query()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $tenantRole->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return $user;
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User}
     */
    protected function makeTenantContext(string $email): array
    {
        $plan = SubscriptionPlan::query()->firstOrCreate([
            'code' => 'workflow-v2-plan',
        ], [
            'name' => 'Workflow V2 Plan',
            'included_modules' => ['core', 'workflow', 'procurement'],
        ]);

        $user = User::query()->create([
            'name' => 'Admin '.$email,
            'email' => $email,
            'password' => 'password',
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-'.Str::slug($email),
            'name' => 'Tenant '.$email,
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => 'org-'.Str::slug($email),
            'name' => 'Org '.$email,
        ]);

        $tenantRole = TenantRole::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Role',
            'slug' => 'admin-role',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::query()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $tenantRole->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $organization, $user];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    protected function pendingArtisan(string $command, array $parameters = []): PendingCommand
    {
        $result = $this->artisan($command, $parameters);

        if ($result instanceof PendingCommand) {
            return $result;
        }

        $this->fail('Expected PendingCommand from artisan(), received an exit code.');
    }
}
