<?php

namespace Modules\Procurement\Services;

use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;

/**
 * Validates a vendor bill payload against PO and GR quantities (three-way match).
 *
 * Returns an array of human-readable errors; empty array means a clean match.
 *
 * Match rules (per PO item):
 *   - billed_qty <= received_qty (cannot bill more than received)
 *   - billed_qty <= ordered_qty  (cannot bill more than ordered)
 *   - billed_unit_price within ±tolerance of PO unit_price (default 5%)
 */
class ThreeWayMatchValidator
{
    public function __construct(public float $priceTolerance = 0.05) {}

    /**
     * @param  array<int, array{purchase_order_item_id:int,quantity:int,unit_price:float}>  $billLines
     * @return array<int, string>
     */
    public function validate(PurchaseOrder $po, GoodsReceipt $gr, array $billLines): array
    {
        $errors = [];

        if ((int) $gr->purchase_order_id !== (int) $po->getKey()
            || (int) $gr->tenant_id !== (int) $po->tenant_id) {
            $errors[] = 'Goods receipt does not belong to the given purchase order and tenant.';
        }

        if ($gr->status !== 'confirmed') {
            $errors[] = 'Goods receipt must be confirmed before billing.';
        }

        if ($billLines === []) {
            $errors[] = 'Vendor bill must contain at least one accepted item.';
        }

        $seenPoItems = [];
        $poItemsById = $po->items->keyBy('id');
        $grQtyByPoItem = $gr->items
            ->groupBy('purchase_order_item_id')
            ->map(fn ($items) => (int) $items->sum('quantity_accepted'));

        foreach ($billLines as $line) {
            $poItemId = (int) ($line['purchase_order_item_id'] ?? 0);
            $billedQty = (int) ($line['quantity'] ?? 0);
            $billedPrice = (float) ($line['unit_price'] ?? 0);

            if (isset($seenPoItems[$poItemId])) {
                $errors[] = "PO item #{$poItemId}: duplicate vendor bill line.";

                continue;
            }

            $seenPoItems[$poItemId] = true;

            if ($billedQty <= 0) {
                $errors[] = "PO item #{$poItemId}: billed quantity must be positive.";
            }

            if (! is_finite($billedPrice) || $billedPrice < 0) {
                $errors[] = "PO item #{$poItemId}: billed unit price must be a non-negative finite number.";
            }

            $poItem = $poItemsById->get($poItemId);
            if (! $poItem) {
                $errors[] = "Bill line references unknown PO item #{$poItemId}.";

                continue;
            }

            $orderedQty = (int) $poItem->quantity;
            $receivedQty = (int) ($grQtyByPoItem[$poItemId] ?? 0);

            if ($billedQty > $receivedQty) {
                $errors[] = "PO item #{$poItemId}: billed quantity ({$billedQty}) exceeds received quantity ({$receivedQty}).";
            }

            if ($billedQty > $orderedQty) {
                $errors[] = "PO item #{$poItemId}: billed quantity ({$billedQty}) exceeds ordered quantity ({$orderedQty}).";
            }

            $poPrice = (float) $poItem->unit_price;
            if ($poPrice <= 0 && $billedPrice > 0) {
                $errors[] = "PO item #{$poItemId}: billed unit price exceeds the zero-price purchase order.";
            } elseif ($poPrice > 0 && is_finite($billedPrice) && abs($billedPrice - $poPrice) / $poPrice > $this->priceTolerance) {
                $errors[] = sprintf(
                    'PO item #%d: billed unit price %.2f deviates from PO price %.2f beyond %.0f%% tolerance.',
                    $poItemId, $billedPrice, $poPrice, $this->priceTolerance * 100,
                );
            }
        }

        return $errors;
    }
}
