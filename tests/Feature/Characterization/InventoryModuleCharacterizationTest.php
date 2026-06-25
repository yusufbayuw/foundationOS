<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Services\WarehouseResolver;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

/**
 * Characterization tests for Inventory warehouse resolution behavior.
 */
class InventoryModuleCharacterizationTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_default_for_tenant_creates_wh_default_when_no_warehouse_exists(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'inventory']);

        $warehouse = app(WarehouseResolver::class)->defaultForTenant($tenant->id);

        $this->assertSame('WH-DEFAULT', $warehouse->code);
        $this->assertSame('Default Warehouse', $warehouse->name);
        $this->assertTrue($warehouse->is_default);
        $this->assertTrue($warehouse->is_active);
    }

    public function test_default_for_tenant_prefers_marked_default_active_warehouse(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'inventory']);

        Warehouse::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'WH-OLD',
            'name' => 'Older Warehouse',
            'is_default' => false,
            'is_active' => true,
        ]);

        $preferred = Warehouse::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'WH-PREF',
            'name' => 'Preferred Warehouse',
            'is_default' => true,
            'is_active' => true,
        ]);

        $resolved = app(WarehouseResolver::class)->defaultForTenant($tenant->id, $organization->id);

        $this->assertSame($preferred->id, $resolved->id);
        $this->assertSame('WH-PREF', $resolved->code);
    }
}
