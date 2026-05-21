<?php

namespace Tests\Feature;

use App\Scopes\TenantScope;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Procurement\Models\Vendor;
use Tests\TestCase;

class TenantScopeIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(CurrentTenant::class)->forget();
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    protected function makeTenant(string $code): Tenant
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'isolation-test'],
            ['name' => 'Isolation Test', 'included_modules' => ['core', 'procurement']],
        );

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => ucfirst($code),
            'subscription_plan_id' => $plan->id,
        ]);
    }

    public function test_vendor_queries_are_scoped_to_the_active_tenant(): void
    {
        $tenantA = $this->makeTenant('tenant-a');
        $tenantB = $this->makeTenant('tenant-b');

        Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'Vendor A1',
        ]);
        Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'Vendor A2',
        ]);
        Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'Vendor B1',
        ]);

        app(CurrentTenant::class)->set($tenantA);
        $this->assertSame(2, Vendor::count());
        $this->assertSame(['Vendor A1', 'Vendor A2'], Vendor::orderBy('name')->pluck('name')->all());

        app(CurrentTenant::class)->set($tenantB);
        $this->assertSame(1, Vendor::count());
        $this->assertSame('Vendor B1', Vendor::first()->name);

        app(CurrentTenant::class)->forget();
        $this->assertSame(3, Vendor::count(), 'no tenant context should expose all rows');
    }

    public function test_creating_a_model_auto_fills_tenant_id_from_context(): void
    {
        $tenant = $this->makeTenant('tenant-auto');

        app(CurrentTenant::class)->set($tenant);

        $vendor = Vendor::create(['name' => 'Auto Filled Vendor']);

        $this->assertSame($tenant->id, $vendor->tenant_id);
    }

    public function test_without_tenant_scope_bypasses_the_global_scope(): void
    {
        $tenantA = $this->makeTenant('scope-a');
        $tenantB = $this->makeTenant('scope-b');

        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantA->id, 'name' => 'X']);
        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantB->id, 'name' => 'Y']);

        app(CurrentTenant::class)->set($tenantA);

        $this->assertSame(1, Vendor::count());
        $this->assertSame(2, Vendor::withoutTenantScope()->count());
        $this->assertSame(2, Vendor::allTenants()->count());
    }

    public function test_for_tenant_runs_callback_with_temporary_tenant_context(): void
    {
        $tenantA = $this->makeTenant('for-a');
        $tenantB = $this->makeTenant('for-b');

        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantA->id, 'name' => 'A']);
        Vendor::withoutTenantScope()->create(['tenant_id' => $tenantB->id, 'name' => 'B']);

        app(CurrentTenant::class)->set($tenantA);

        $names = app(CurrentTenant::class)->forTenant($tenantB, fn () => Vendor::pluck('name')->all());

        $this->assertSame(['B'], $names);
        $this->assertSame($tenantA->id, app(CurrentTenant::class)->id(), 'context should be restored after callback');
    }

    public function test_tenant_scope_resolves_null_when_no_context_is_bound(): void
    {
        app(CurrentTenant::class)->forget();

        $this->assertNull(TenantScope::resolveTenantId());
    }
}
