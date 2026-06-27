<?php

namespace Modules\Procurement\Services;

use App\Support\TypedValue;
use Illuminate\Support\Facades\DB;
use Modules\Procurement\Events\GoodsReceiptConfirmed;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\PurchaseOrder;

/**
 * Creates a GoodsReceipt for a PO, supporting partial receipt.
 *
 * Updates the parent PO status:
 *   - any received < ordered  → status = 'partial'
 *   - all received >= ordered → status = 'received'
 *   - explicit close          → status = 'closed'
 *
 * Caller passes a quantity map keyed by PO item id; missing items default
 * to receiving the full remaining quantity.
 */
class GoodsReceiptAutoCreationService
{
    /**
     * @param  array<int,int>  $quantities  keyed by purchase_order_item_id → quantity received
     */
    public function receive(PurchaseOrder $purchaseOrder, array $quantities = [], ?int $receivedBy = null, ?string $notes = null): GoodsReceipt
    {
        return DB::transaction(function () use ($purchaseOrder, $quantities, $receivedBy, $notes): GoodsReceipt {
            $purchaseOrder = TypedValue::model($purchaseOrder->fresh(['items']));

            $receipt = GoodsReceipt::query()->create([
                'tenant_id' => $purchaseOrder->tenant_id,
                'purchase_order_id' => $purchaseOrder->getKey(),
                'received_by' => $receivedBy,
                'receipt_number' => $this->generateReceiptNumber($purchaseOrder),
                'receipt_date' => now()->toDateString(),
                'status' => 'confirmed',
                'received_at' => now(),
                'notes' => $notes,
            ]);

            foreach ($purchaseOrder->items as $poItem) {
                $remaining = max(0, (int) $poItem->quantity - (int) $poItem->quantity_received);
                $qty = $quantities[$poItem->id] ?? $remaining;
                $qty = max(0, min((int) $qty, $remaining));

                if ($qty <= 0) {
                    continue;
                }

                GoodsReceiptItem::query()->create([
                    'tenant_id' => $receipt->tenant_id,
                    'goods_receipt_id' => $receipt->getKey(),
                    'purchase_order_item_id' => $poItem->id,
                    'quantity_received' => $qty,
                    'quantity_accepted' => $qty,
                    'quantity_rejected' => 0,
                    'unit_price' => $poItem->unit_price,
                    'total_amount' => round((float) $poItem->unit_price * $qty, 2),
                ]);

                $poItem->forceFill([
                    'quantity_received' => (int) $poItem->quantity_received + $qty,
                ])->save();
            }

            $this->recomputePoStatus($purchaseOrder);

            $receipt = TypedValue::model($receipt->fresh(['items']));

            GoodsReceiptConfirmed::dispatch($receipt);

            return $receipt;
        });
    }

    public function close(PurchaseOrder $purchaseOrder): void
    {
        $purchaseOrder->forceFill(['status' => 'closed'])->save();
    }

    protected function recomputePoStatus(PurchaseOrder $purchaseOrder): void
    {
        $purchaseOrder = TypedValue::model($purchaseOrder->fresh(['items']));

        $totalOrdered = TypedValue::int($purchaseOrder->items->sum('quantity'));
        $totalReceived = TypedValue::int($purchaseOrder->items->sum('quantity_received'));

        $status = match (true) {
            $totalReceived <= 0 => $purchaseOrder->status,
            $totalReceived >= $totalOrdered => 'received',
            default => 'partial',
        };

        if ($purchaseOrder->status !== $status) {
            $purchaseOrder->forceFill(['status' => $status])->save();
        }
    }

    protected function generateReceiptNumber(PurchaseOrder $purchaseOrder): string
    {
        $base = 'GR-'.TypedValue::string($purchaseOrder->po_number ?: $purchaseOrder->getKey());
        $candidate = $base;
        $seq = 1;

        while (GoodsReceipt::withoutTenantScope()
            ->where('tenant_id', $purchaseOrder->tenant_id)
            ->where('receipt_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $base.'-'.$seq;
        }

        return $candidate;
    }
}
