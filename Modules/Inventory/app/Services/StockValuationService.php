<?php

namespace Modules\Inventory\Services;

use Modules\Inventory\Enums\ValuationMethod;
use Modules\Inventory\Models\StockCostLayer;
use Modules\Inventory\Models\StockItem;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\Warehouse;
use RuntimeException;

class StockValuationService
{
    /**
     * @return array{unit_cost: float, total_cost: float}
     */
    public function resolveOutboundCost(
        StockItem $stockItem,
        Warehouse $warehouse,
        float $quantity,
    ): array {
        return match ($stockItem->valuation_method) {
            ValuationMethod::Fifo => $this->consumeLayers($stockItem, $warehouse, $quantity, ascending: true),
            ValuationMethod::Lifo => $this->consumeLayers($stockItem, $warehouse, $quantity, ascending: false),
            ValuationMethod::Avg => $this->averageCost($stockItem, $warehouse, $quantity),
        };
    }

    /**
     * @return array{unit_cost: float, total_cost: float}
     */
    protected function averageCost(StockItem $stockItem, Warehouse $warehouse, float $quantity): array
    {
        $level = StockLevel::withoutTenantScope()
            ->where('warehouse_id', $warehouse->getKey())
            ->where('stock_item_id', $stockItem->getKey())
            ->first();

        $unitCost = (float) ($level?->average_unit_cost ?? 0);

        return [
            'unit_cost' => $unitCost,
            'total_cost' => round($unitCost * $quantity, 2),
        ];
    }

    /**
     * @return array{unit_cost: float, total_cost: float}
     */
    protected function consumeLayers(
        StockItem $stockItem,
        Warehouse $warehouse,
        float $quantity,
        bool $ascending,
    ): array {
        $remaining = $quantity;
        $totalCost = 0.0;

        $layers = StockCostLayer::withoutTenantScope()
            ->where('warehouse_id', $warehouse->getKey())
            ->where('stock_item_id', $stockItem->getKey())
            ->where('quantity_remaining', '>', 0)
            ->orderBy('id', $ascending ? 'asc' : 'desc')
            ->lockForUpdate()
            ->get();

        foreach ($layers as $layer) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, (float) $layer->quantity_remaining);
            $totalCost += $take * (float) $layer->unit_cost;
            $layer->forceFill([
                'quantity_remaining' => (float) $layer->quantity_remaining - $take,
            ])->save();
            $remaining -= $take;
        }

        if ($remaining > 0.0001) {
            throw new RuntimeException('Insufficient stock cost layers for outbound move.');
        }

        $unitCost = $quantity > 0 ? $totalCost / $quantity : 0;

        return [
            'unit_cost' => round($unitCost, 4),
            'total_cost' => round($totalCost, 2),
        ];
    }

    public function recordInboundLayer(
        int $tenantId,
        Warehouse $warehouse,
        StockItem $stockItem,
        int $stockMoveId,
        float $quantity,
        float $unitCost,
    ): void {
        if ($stockItem->valuation_method === ValuationMethod::Avg) {
            return;
        }

        StockCostLayer::query()->create([
            'tenant_id' => $tenantId,
            'warehouse_id' => $warehouse->getKey(),
            'stock_item_id' => $stockItem->getKey(),
            'stock_move_id' => $stockMoveId,
            'quantity_remaining' => $quantity,
            'unit_cost' => $unitCost,
        ]);
    }

    public function updateAverageOnInbound(StockLevel $level, float $inboundQty, float $unitCost): void
    {
        $onHand = (float) $level->quantity_on_hand;
        $currentAvg = (float) $level->average_unit_cost;
        $newQty = $onHand + $inboundQty;

        if ($newQty <= 0) {
            $level->forceFill(['average_unit_cost' => $unitCost]);

            return;
        }

        $newAvg = (($onHand * $currentAvg) + ($inboundQty * $unitCost)) / $newQty;
        $level->forceFill(['average_unit_cost' => round($newAvg, 4)]);
    }
}
