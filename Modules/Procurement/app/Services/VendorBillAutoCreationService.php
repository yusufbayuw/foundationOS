<?php

namespace Modules\Procurement\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\JournalEntry;
use Modules\Procurement\Exceptions\ThreeWayMatchException;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Models\VendorBillItem;

/**
 * Creates a VendorBill from a confirmed GoodsReceipt, enforcing three-way
 * matching (PO ↔ GR ↔ Invoice) and posting a draft journal entry.
 *
 * If no explicit bill lines are passed, defaults to billing exactly what
 * has been received & accepted at PO unit price.
 */
class VendorBillAutoCreationService
{
    public function __construct(private readonly ThreeWayMatchValidator $matcher) {}

    /**
     * @param  array<int, array{purchase_order_item_id:int,quantity:int,unit_price:float,description?:string,unit_of_measure?:string}>|null  $billLines
     */
    public function createFromGoodsReceipt(GoodsReceipt $receipt, ?array $billLines = null, ?int $processedBy = null): VendorBill
    {
        return DB::transaction(function () use ($receipt, $billLines, $processedBy): VendorBill {
            $receipt = $receipt->fresh(['items', 'purchaseOrder.items']);
            $po = $receipt->purchaseOrder;

            if (! $po) {
                throw new ThreeWayMatchException(['Goods receipt has no parent purchase order.']);
            }

            $billLines = $billLines ?? $this->defaultBillLines($po, $receipt);

            $errors = $this->matcher->validate($po, $receipt, $billLines);
            if (! empty($errors)) {
                throw new ThreeWayMatchException($errors);
            }

            $subtotal = array_sum(array_map(
                fn (array $line) => (int) $line['quantity'] * (float) $line['unit_price'],
                $billLines,
            ));

            $bill = VendorBill::query()->create([
                'tenant_id' => $receipt->tenant_id,
                'vendor_id' => $po->vendor_id,
                'purchase_order_id' => $po->getKey(),
                'goods_receipt_id' => $receipt->getKey(),
                'processed_by' => $processedBy,
                'bill_number' => $this->generateBillNumber($receipt),
                'bill_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'status' => 'confirmed',
                'payment_status' => 'unpaid',
                'processed_at' => now(),
            ]);

            $grItemByPoItem = $receipt->items->keyBy('purchase_order_item_id');

            foreach ($billLines as $line) {
                $poItemId = (int) $line['purchase_order_item_id'];
                $qty = (int) $line['quantity'];
                $unitPrice = (float) $line['unit_price'];

                VendorBillItem::query()->create([
                    'tenant_id' => $bill->tenant_id,
                    'vendor_bill_id' => $bill->getKey(),
                    'purchase_order_item_id' => $poItemId,
                    'goods_receipt_item_id' => $grItemByPoItem->get($poItemId)?->id,
                    'description' => $line['description'] ?? null,
                    'quantity' => $qty,
                    'unit_of_measure' => $line['unit_of_measure'] ?? null,
                    'unit_price' => $unitPrice,
                    'line_total' => round($qty * $unitPrice, 2),
                ]);
            }

            $journal = $this->postDraftJournal($bill);
            $bill->forceFill(['journal_entry_id' => $journal->id])->save();

            return $bill->fresh(['items', 'journalEntry']);
        });
    }

    /**
     * @return array<int, array{purchase_order_item_id:int,quantity:int,unit_price:float,description:?string,unit_of_measure:?string}>
     */
    protected function defaultBillLines(PurchaseOrder $po, GoodsReceipt $receipt): array
    {
        $poItemsById = $po->items->keyBy('id');

        return $receipt->items
            ->filter(fn ($gr) => $gr->purchase_order_item_id !== null && (int) $gr->quantity_accepted > 0)
            ->map(function ($gr) use ($poItemsById): array {
                $poItem = $poItemsById->get($gr->purchase_order_item_id);

                return [
                    'purchase_order_item_id' => (int) $gr->purchase_order_item_id,
                    'quantity' => (int) $gr->quantity_accepted,
                    'unit_price' => (float) ($poItem->unit_price ?? $gr->unit_price),
                    'description' => $poItem?->description,
                    'unit_of_measure' => $poItem?->unit_of_measure,
                ];
            })
            ->values()
            ->all();
    }

    protected function postDraftJournal(VendorBill $bill): JournalEntry
    {
        $organizationId = Organization::withoutTenantScope()
            ->where('tenant_id', $bill->tenant_id)
            ->orderBy('id')
            ->value('id');

        return JournalEntry::query()->create([
            'tenant_id' => $bill->tenant_id,
            'organization_id' => $organizationId,
            'entry_number' => 'JE-'.$bill->bill_number,
            'date' => $bill->bill_date,
            'description' => sprintf('Auto-journal vendor bill %s (PO %s)',
                $bill->bill_number,
                $bill->purchaseOrder?->po_number,
            ),
            'total_debit' => $bill->total_amount,
            'total_credit' => $bill->total_amount,
            'is_balanced' => true,
            'is_posted' => false,
        ]);
    }

    protected function generateBillNumber(GoodsReceipt $receipt): string
    {
        $base = 'BILL-'.($receipt->receipt_number ?: $receipt->getKey());
        $candidate = $base;
        $seq = 1;

        while (VendorBill::withoutTenantScope()
            ->where('tenant_id', $receipt->tenant_id)
            ->where('bill_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $base.'-'.$seq;
        }

        return $candidate;
    }
}
