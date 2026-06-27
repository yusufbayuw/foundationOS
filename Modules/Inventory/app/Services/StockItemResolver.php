<?php

namespace Modules\Inventory\Services;

use App\Support\TypedValue;
use Modules\Inventory\Enums\ValuationMethod;
use Modules\Inventory\Models\StockItem;
use Modules\Procurement\Models\ProcurementItem;

class StockItemResolver
{
    public function findOrCreateFromProcurementItem(ProcurementItem $procurementItem): StockItem
    {
        $existing = StockItem::withoutTenantScope()
            ->where('tenant_id', $procurementItem->tenant_id)
            ->where('procurement_item_id', $procurementItem->getKey())
            ->first();

        if ($existing) {
            return $existing;
        }

        return StockItem::query()->create([
            'tenant_id' => $procurementItem->tenant_id,
            'organization_id' => null,
            'procurement_item_id' => $procurementItem->getKey(),
            'inventory_coa_id' => $procurementItem->chart_of_account_id,
            'code' => $procurementItem->code ?: 'ITEM-'.TypedValue::string($procurementItem->getKey()),
            'name' => $procurementItem->name,
            'unit_of_measure' => $procurementItem->unit_of_measure,
            'valuation_method' => ValuationMethod::Avg,
            'is_active' => true,
        ]);
    }
}
