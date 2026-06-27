<?php

namespace Modules\Inventory\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Inventory\Enums\StockMoveStatus;
use Modules\Inventory\Enums\StockMoveType;
use Modules\Inventory\Events\StockMoveCommitted;
use Modules\Inventory\Models\StockItem;
use Modules\Inventory\Models\StockLevel;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;
use RuntimeException;

class StockMoveService
{
    public function __construct(
        private readonly StockValuationService $valuation,
    ) {}

    public function commitInbound(
        Warehouse $warehouse,
        StockItem $stockItem,
        float $quantity,
        float $unitCost,
        ?Model $reference = null,
        ?string $notes = null,
    ): StockMove {
        return $this->commit(
            warehouse: $warehouse,
            stockItem: $stockItem,
            moveType: StockMoveType::In,
            quantity: $quantity,
            unitCost: $unitCost,
            reference: $reference,
            notes: $notes,
        );
    }

    public function commitOutbound(
        Warehouse $warehouse,
        StockItem $stockItem,
        float $quantity,
        ?Model $reference = null,
        ?string $notes = null,
    ): StockMove {
        $cost = $this->valuation->resolveOutboundCost($stockItem, $warehouse, $quantity);

        return $this->commit(
            warehouse: $warehouse,
            stockItem: $stockItem,
            moveType: StockMoveType::Out,
            quantity: $quantity,
            unitCost: $cost['unit_cost'],
            reference: $reference,
            notes: $notes,
        );
    }

    protected function commit(
        Warehouse $warehouse,
        StockItem $stockItem,
        StockMoveType $moveType,
        float $quantity,
        float $unitCost,
        ?Model $reference = null,
        ?string $notes = null,
    ): StockMove {
        if ($quantity <= 0) {
            throw new RuntimeException('Stock move quantity must be positive.');
        }

        return DB::transaction(function () use ($warehouse, $stockItem, $moveType, $quantity, $unitCost, $reference, $notes): StockMove {
            $organizationId = Organization::withoutTenantScope()
                ->where('tenant_id', $stockItem->tenant_id)
                ->orderBy('id')
                ->value('id');

            $level = StockLevel::query()
                ->where('warehouse_id', $warehouse->getKey())
                ->where('stock_item_id', $stockItem->getKey())
                ->lockForUpdate()
                ->first();

            if (! $level) {
                $level = StockLevel::query()->create([
                    'tenant_id' => $stockItem->tenant_id,
                    'warehouse_id' => $warehouse->getKey(),
                    'stock_item_id' => $stockItem->getKey(),
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'average_unit_cost' => 0,
                ]);
            }

            if ($moveType === StockMoveType::Out && (float) $level->quantity_on_hand < $quantity) {
                throw new RuntimeException('Insufficient stock on hand.');
            }

            $totalCost = round($quantity * $unitCost, 2);

            $move = StockMove::query()->create([
                'tenant_id' => $stockItem->tenant_id,
                'organization_id' => $organizationId,
                'warehouse_id' => $warehouse->getKey(),
                'stock_item_id' => $stockItem->getKey(),
                'move_number' => $this->generateMoveNumber(TypedValue::int($stockItem->tenant_id), $moveType),
                'move_type' => $moveType,
                'status' => StockMoveStatus::Committed,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'moved_at' => now(),
                'committed_at' => now(),
                'notes' => $notes,
            ]);

            if ($moveType === StockMoveType::In) {
                $this->valuation->updateAverageOnInbound($level, $quantity, $unitCost);
                $level->forceFill([
                    'quantity_on_hand' => (float) $level->quantity_on_hand + $quantity,
                ])->save();

                $this->valuation->recordInboundLayer(
                    (int) $stockItem->tenant_id,
                    $warehouse,
                    $stockItem,
                    TypedValue::int($move->getKey()),
                    $quantity,
                    $unitCost,
                );
            } else {
                $level->forceFill([
                    'quantity_on_hand' => (float) $level->quantity_on_hand - $quantity,
                ])->save();
            }

            $freshMove = TypedValue::model($move->fresh(['stockItem', 'warehouse']));
            event(new StockMoveCommitted($freshMove));

            return $move;
        });
    }

    protected function generateMoveNumber(int $tenantId, StockMoveType $type): string
    {
        $prefix = $type === StockMoveType::In ? 'SM-IN' : 'SM-OUT';
        $candidate = $prefix.'-'.now()->format('YmdHis');
        $seq = 1;

        while (StockMove::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('move_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix.'-'.now()->format('YmdHis').'-'.$seq;
        }

        return $candidate;
    }
}
