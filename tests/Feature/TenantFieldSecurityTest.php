<?php

namespace Tests\Feature;

use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Tests\TestCase;

class TenantFieldSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_tenant_field_ignores_client_supplied_tenant_id(): void
    {
        [$tenantA, $tenantB] = $this->createTwoTenants();

        Filament::shouldReceive('getTenant')->andReturn($tenantA);

        $dehydrate = $this->resolveDehydrateCallback(TenantField::make());

        $this->assertSame($tenantA->id, $dehydrate($tenantB->id));
        $this->assertSame($tenantA->id, $dehydrate($tenantA->id));
    }

    public function test_organization_hidden_field_ignores_client_supplied_organization_id(): void
    {
        [$tenant, $user, $primaryOrganization, $secondaryOrganization] = $this->createTenantWithTwoOrganizations();

        Filament::shouldReceive('getTenant')->andReturn($tenant);
        $this->actingAs($user);

        $dehydrate = $this->resolveDehydrateCallback(TenantField::organizationHidden());

        $this->assertSame($primaryOrganization->id, $dehydrate($secondaryOrganization->id));
    }

    /**
     * @return \Closure(mixed): (int|string|null)
     */
    private function resolveDehydrateCallback(Field $field): \Closure
    {
        $reflection = new \ReflectionClass($field);

        while ($reflection !== false) {
            if ($reflection->hasProperty('dehydrateStateUsing')) {
                $property = $reflection->getProperty('dehydrateStateUsing');
                $property->setAccessible(true);
                $callback = $property->getValue($field);

                $this->assertInstanceOf(\Closure::class, $callback);

                return $callback;
            }

            $reflection = $reflection->getParentClass();
        }

        $this->fail('dehydrateStateUsing callback was not configured on the field.');
    }

    /**
     * @return array{0: Tenant, 1: Tenant}
     */
    private function createTwoTenants(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        $tenantA = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-a',
            'name' => 'Tenant A',
            'subscription_plan_id' => $plan->id,
        ]);

        $tenantB = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-b',
            'name' => 'Tenant B',
            'subscription_plan_id' => $plan->id,
        ]);

        return [$tenantA, $tenantB];
    }

    /**
     * @return array{0: Tenant, 1: User, 2: Organization, 3: Organization}
     */
    private function createTenantWithTwoOrganizations(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        $user = User::create([
            'name' => 'Tenant Admin',
            'email' => 'tenant-field-'.Str::random(5).'@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-field',
            'name' => 'Tenant Field Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $primaryOrganization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Campus',
            'is_main' => true,
        ]);

        $secondaryOrganization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'branch',
            'name' => 'Branch Campus',
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'slug' => 'admin',
            'permissions' => ['*'],
            'is_default' => true,
            'is_super_admin' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $primaryOrganization->id,
            'tenant_role_id' => $role->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $user, $primaryOrganization, $secondaryOrganization];
    }
}
