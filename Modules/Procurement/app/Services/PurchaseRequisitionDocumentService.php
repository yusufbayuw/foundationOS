<?php

namespace Modules\Procurement\Services;

use App\Support\TypedValue;
use Modules\Procurement\Models\PurchaseRequisition;

class PurchaseRequisitionDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(PurchaseRequisition $requisition): array
    {
        $requisition->load([
            'items.procurementItem',
            'items.preferredVendor',
            'requester',
            'approver',
        ]);

        return [
            'requisition' => $requisition,
            'showSignature' => true,
            'signatureLabel' => 'Pengadaan',
        ];
    }

    public function filename(PurchaseRequisition $requisition): string
    {
        return sprintf('PurchaseRequisition_%s.pdf', str_replace(' ', '_', $requisition->request_number ?? TypedValue::string($requisition->getKey())));
    }
}
