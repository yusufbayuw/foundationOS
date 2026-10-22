<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Enums\StockMoveStatus;
use Modules\Inventory\Enums\StockMoveType;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;

class GoodsReceiptStockService
{
    public function __construct(
        private readonly WarehouseResolver $warehouseResolver,
        private readonly StockItemResolver $stockItemResolver,
        private readonly StockMoveService $stockMoveService,
    ) {}

    /**
     * @return array<int, StockMove>
     */
    public function receiveFromGoodsReceipt(GoodsReceipt $receipt): array
    {
        return DB::transaction(function () use ($receipt): array {
            $receipt = GoodsReceipt::query()
                ->whereKey($receipt->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($receipt->status !== 'confirmed') {
                return [];
            }

            $receipt->load(['items.purchaseOrderItem.procurementItem', 'purchaseOrder']);
            $warehouse = $this->warehouseResolver->defaultForTenant(
                (int) $receipt->tenant_id,
                $receipt->purchaseOrder?->organization_id,
            );

            $moves = [];

            foreach ($receipt->items as $grItem) {
                $move = $this->receiveLine($warehouse, $grItem);
                if ($move) {
                    $moves[] = $move;
                }
            }

            return $moves;
        });
    }

    protected function receiveLine(
        Warehouse $warehouse,
        GoodsReceiptItem $grItem,
    ): ?StockMove {
        $qty = (int) $grItem->quantity_accepted;
        if ($qty <= 0) {
            return null;
        }

        // A replayed receipt-confirmed event must never increase stock twice.
        $alreadyCommitted = StockMove::withoutTenantScope()
            ->withTrashed()
            ->where('tenant_id', $grItem->tenant_id)
            ->where('reference_type', $grItem->getMorphClass())
            ->where('reference_id', $grItem->getKey())
            ->where('move_type', StockMoveType::In->value)
            ->where('status', StockMoveStatus::Committed->value)
            ->exists();

        if ($alreadyCommitted) {
            return null;
        }

        $poItem = $grItem->purchaseOrderItem;
        $procurementItem = $poItem?->procurementItem;

        if (! $procurementItem) {
            return null;
        }

        $stockItem = $this->stockItemResolver->findOrCreateFromProcurementItem($procurementItem);
        $unitCost = (float) ($grItem->unit_price ?? $poItem->unit_price ?? 0);

        return $this->stockMoveService->commitInbound(
            warehouse: $warehouse,
            stockItem: $stockItem,
            quantity: $qty,
            unitCost: $unitCost,
            reference: $grItem,
            notes: 'Auto stock-in from goods receipt',
        );
    }
}
