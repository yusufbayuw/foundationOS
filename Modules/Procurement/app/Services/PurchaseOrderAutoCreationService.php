<?php

namespace Modules\Procurement\Services;

use Illuminate\Support\Facades\DB;
use Modules\Procurement\Exceptions\RfqAwardException;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\RfqVendor;

/**
 * Creates a draft PurchaseOrder from an awarded RfqVendor.
 *
 * - Marks the RfqVendor as awarded and locks the parent RFQ
 *   (status → `awarded`) so its items can no longer be edited.
 * - Copies RFQ line items into PO items, using the vendor's quotation
 *   amount when present (otherwise the RFQ estimated budget).
 * - Inherits tenant_id and currency from the RFQ.
 * - Idempotent: if a draft PO already exists for the awarded vendor,
 *   the existing one is returned.
 */
class PurchaseOrderAutoCreationService
{
    public function awardToVendor(RfqVendor $rfqVendor, ?int $createdBy = null, ?string $awardReason = null): PurchaseOrder
    {
        return DB::transaction(function () use ($rfqVendor, $awardReason): PurchaseOrder {
            $rfqVendor = $rfqVendor->fresh(['requestForQuotation.items', 'vendor']);
            $rfq = $rfqVendor->requestForQuotation;

            if (! $rfq) {
                throw new RfqAwardException('RFQ not found for the awarded vendor.');
            }

            if ($rfq->status === 'awarded' && ! $rfqVendor->is_awarded) {
                throw new RfqAwardException('RFQ is already awarded to a different vendor.');
            }

            $existing = PurchaseOrder::query()
                ->where('request_for_quotation_id', $rfq->getKey())
                ->where('vendor_id', $rfqVendor->vendor_id)
                ->where('status', 'draft')
                ->first();

            if ($existing) {
                $this->lockRfq($rfqVendor, $awardReason);

                return $existing;
            }

            $subtotal = (float) ($rfqVendor->quotation_amount ?? $rfq->total_estimated_budget);

            $po = PurchaseOrder::query()->create([
                'tenant_id' => $rfq->tenant_id,
                'request_for_quotation_id' => $rfq->getKey(),
                'vendor_id' => $rfqVendor->vendor_id,
                'po_number' => $this->generatePoNumber($rfq),
                'po_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'currency' => $rfq->currency ?: 'IDR',
                'exchange_rate' => 1,
                'status' => 'draft',
                'notes' => sprintf('Auto-generated from RFQ %s (vendor %s).',
                    $rfq->rfq_number,
                    $rfqVendor->vendor->name ?? '#'.$rfqVendor->vendor_id,
                ),
            ]);

            $items = $rfq->items;
            $itemCount = max(1, $items->count());
            $perItemBudget = $subtotal / $itemCount;

            foreach ($items as $rfqItem) {
                $quantity = max(1, (int) $rfqItem->quantity);
                $unitPrice = $perItemBudget / $quantity;

                PurchaseOrderItem::query()->create([
                    'tenant_id' => $po->tenant_id,
                    'purchase_order_id' => $po->getKey(),
                    'procurement_item_id' => $rfqItem->procurement_item_id,
                    'description' => $rfqItem->description,
                    'specifications' => $rfqItem->specifications,
                    'quantity' => $quantity,
                    'unit_of_measure' => $rfqItem->unit_of_measure,
                    'unit_price' => round($unitPrice, 2),
                    'line_total' => round($unitPrice * $quantity, 2),
                ]);
            }

            $this->lockRfq($rfqVendor, $awardReason);

            return $po->fresh(['items']);
        });
    }

    protected function lockRfq(RfqVendor $rfqVendor, ?string $awardReason): void
    {
        $rfqVendor->forceFill([
            'is_awarded' => true,
            'is_shortlisted' => true,
            'status' => 'awarded',
            'award_reason' => $awardReason ?: $rfqVendor->award_reason,
        ])->save();

        // Demote any sibling vendors from awarded state.
        RfqVendor::query()
            ->where('request_for_quotation_id', $rfqVendor->request_for_quotation_id)
            ->where('id', '!=', $rfqVendor->getKey())
            ->update(['is_awarded' => false]);

        $rfqVendor->requestForQuotation?->forceFill(['status' => 'awarded'])->save();
    }

    protected function generatePoNumber(RequestForQuotation $rfq): string
    {
        $base = 'PO-'.($rfq->rfq_number ?: $rfq->getKey());
        $candidate = $base;
        $seq = 1;

        while (PurchaseOrder::withoutTenantScope()
            ->where('tenant_id', $rfq->tenant_id)
            ->where('po_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $base.'-'.$seq;
        }

        return $candidate;
    }
}
