<?php

namespace Modules\Inventory\Services;

use Modules\Inventory\Models\Warehouse;

class WarehouseResolver
{
    public function defaultForTenant(int $tenantId, ?int $organizationId = null): Warehouse
    {
        $warehouse = Warehouse::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->when($organizationId, fn ($q) => $q->where($q->getModel()->qualifyColumn('organization_id'), $organizationId))
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if ($warehouse) {
            return $warehouse;
        }

        $warehouse = Warehouse::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if ($warehouse) {
            return $warehouse;
        }

        return Warehouse::query()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => 'WH-DEFAULT',
            'name' => 'Default Warehouse',
            'is_default' => true,
            'is_active' => true,
        ]);
    }
}
