<?php

namespace Modules\Inventory\Services;

use App\Support\TypedValue;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\StockAdjustment;
use RuntimeException;

class StockAdjustmentService
{
    public function __construct(
        private readonly StockMoveService $stockMoveService,
    ) {}

    public function commit(StockAdjustment $adjustment): StockAdjustment
    {
        if ($adjustment->status === 'committed') {
            return $adjustment;
        }

        return DB::transaction(function () use ($adjustment): StockAdjustment {
            $adjustment = TypedValue::model($adjustment->fresh(['lines.stockItem', 'warehouse']));

            if ($adjustment->lines->isEmpty()) {
                throw new RuntimeException('Stock adjustment has no lines.');
            }

            $totalImpact = 0.0;

            foreach ($adjustment->lines as $line) {
                $delta = (float) $line->quantity_delta;
                if ($delta == 0.0) {
                    continue;
                }

                $unitCost = (float) $line->unit_cost;
                $warehouse = TypedValue::model($adjustment->warehouse, 'Stock adjustment warehouse is required.');
                $stockItem = TypedValue::model($line->stockItem, 'Stock adjustment line stock item is required.');

                if ($delta > 0) {
                    $move = $this->stockMoveService->commitInbound(
                        warehouse: $warehouse,
                        stockItem: $stockItem,
                        quantity: $delta,
                        unitCost: $unitCost,
                        reference: $line,
                        notes: 'Stock adjustment '.$adjustment->adjustment_number,
                    );
                } else {
                    $move = $this->stockMoveService->commitOutbound(
                        warehouse: $warehouse,
                        stockItem: $stockItem,
                        quantity: abs($delta),
                        reference: $line,
                        notes: 'Stock adjustment '.$adjustment->adjustment_number,
                    );
                }

                $line->forceFill(['line_total' => (float) $move->total_cost])->save();
                $totalImpact += (float) $move->total_cost * ($delta > 0 ? 1 : -1);
            }

            $adjustment->forceFill([
                'status' => 'committed',
                'total_value_impact' => round($totalImpact, 2),
                'approved_at' => now(),
            ])->save();

            return TypedValue::model($adjustment->fresh(['lines']));
        });
    }

    public function recalculateTotals(StockAdjustment $adjustment): void
    {
        $total = $adjustment->lines()->sum('line_total');
        $adjustment->forceFill(['total_value_impact' => $total])->save();
    }
}
