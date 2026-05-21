<?php

namespace Modules\Procurement\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\TenantSetting;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\RfqItem;

/**
 * Materializes a draft RequestForQuotation from an approved PurchaseRequisition.
 *
 * - Idempotent: if a draft RFQ already exists for the PR it is returned as-is.
 * - Honors the per-tenant `procurement.auto_create_rfq_from_pr` setting (default on).
 * - Copies line items and total estimated budget from the PR.
 */
class RfqAutoCreationService
{
    public const SETTING_KEY = 'auto_create_rfq_from_pr';

    public const SETTING_GROUP = 'procurement';

    public function isEnabledFor(int $tenantId): bool
    {
        $setting = TenantSetting::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('group', self::SETTING_GROUP)
            ->where('key', self::SETTING_KEY)
            ->first();

        if (! $setting) {
            return true;
        }

        return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
    }

    public function createDraftFor(PurchaseRequisition $requisition): ?RequestForQuotation
    {
        if (! $this->isEnabledFor((int) $requisition->tenant_id)) {
            return null;
        }

        return DB::transaction(function () use ($requisition): RequestForQuotation {
            $existing = RequestForQuotation::query()
                ->where('purchase_requisition_id', $requisition->getKey())
                ->where('status', 'draft')
                ->first();

            if ($existing) {
                return $existing;
            }

            $rfq = RequestForQuotation::query()->create([
                'tenant_id' => $requisition->tenant_id,
                'purchase_requisition_id' => $requisition->getKey(),
                'created_by' => $requisition->approved_by ?? $requisition->requested_by,
                'rfq_number' => $this->generateRfqNumber($requisition),
                'rfq_date' => now()->toDateString(),
                'closing_date' => now()->addDays(7)->toDateString(),
                'description' => sprintf(
                    'Auto-generated from PR %s',
                    (string) $requisition->request_number,
                ),
                'total_estimated_budget' => (float) $requisition->total_estimated_amount,
                'currency' => 'IDR',
                'status' => 'draft',
            ]);

            foreach ($requisition->items as $item) {
                RfqItem::query()->create([
                    'tenant_id' => $rfq->tenant_id,
                    'request_for_quotation_id' => $rfq->getKey(),
                    'procurement_item_id' => $item->procurement_item_id,
                    'description' => $item->description,
                    'specifications' => $item->specifications,
                    'quantity' => (int) $item->quantity_requested,
                    'unit_of_measure' => $item->unit_of_measure,
                    'estimated_budget' => (float) $item->estimated_total_price,
                ]);
            }

            return $rfq;
        });
    }

    protected function generateRfqNumber(PurchaseRequisition $requisition): string
    {
        $base = 'RFQ-'.($requisition->request_number ?: $requisition->getKey());
        $candidate = $base;
        $seq = 1;

        while (RequestForQuotation::withoutTenantScope()
            ->where('tenant_id', $requisition->tenant_id)
            ->where('rfq_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $base.'-'.$seq;
        }

        return $candidate;
    }
}
