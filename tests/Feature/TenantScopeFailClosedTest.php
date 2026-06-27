<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Exceptions\MissingTenantContextException;
use Modules\Procurement\Models\Vendor;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class TenantScopeFailClosedTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        config(['tenancy.scope_fail_closed' => false]);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_scoped_query_without_tenant_throws_when_fail_closed_enabled(): void
    {
        config(['tenancy.scope_fail_closed' => true]);
        app(CurrentTenant::class)->forget();

        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'procurement']);

        Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'V001',
            'name' => 'Test Vendor',
            'status' => 'active',
        ]);

        $this->expectException(MissingTenantContextException::class);

        Vendor::query()->first();
    }

    public function test_scoped_query_succeeds_when_tenant_is_bound(): void
    {
        config(['tenancy.scope_fail_closed' => true]);

        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'procurement']);

        $vendor = Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'V002',
            'name' => 'Scoped Vendor',
            'status' => 'active',
        ]);

        app(CurrentTenant::class)->set($tenant);

        $this->assertTrue(Vendor::query()->whereKey($vendor->id)->exists());
    }
}
