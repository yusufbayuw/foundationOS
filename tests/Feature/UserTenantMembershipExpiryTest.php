<?php

namespace Tests\Feature;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Tests\TestCase;

class UserTenantMembershipExpiryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_expired_membership_denies_tenant_access(): void
    {
        $user = User::factory()->create();
        $tenant = $this->makeTenant();

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'tenant_role_id' => TenantRole::create([
                'tenant_id' => $tenant->id,
                'name' => 'Staff',
                'slug' => 'staff',
                'permissions' => ['*'],
            ])->id,
            'assigned_by' => $user->id,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($user->canAccessTenant($tenant));
        $this->assertCount(0, $user->getTenants(Filament::getPanel('admin')));
    }

    public function test_active_membership_allows_tenant_access(): void
    {
        $user = User::factory()->create();
        $tenant = $this->makeTenant();

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'tenant_role_id' => TenantRole::create([
                'tenant_id' => $tenant->id,
                'name' => 'Staff',
                'slug' => 'staff-active',
                'permissions' => ['*'],
            ])->id,
            'assigned_by' => $user->id,
            'expires_at' => now()->addMonth(),
        ]);

        $this->assertTrue($user->canAccessTenant($tenant));
        $this->assertCount(1, $user->getTenants(Filament::getPanel('admin')));
    }

    protected function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'expiry-test',
            'name' => 'Expiry Test',
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exp-tenant',
            'name' => 'Expiry Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
