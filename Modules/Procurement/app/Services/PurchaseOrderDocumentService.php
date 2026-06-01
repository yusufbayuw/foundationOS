<?php

namespace Modules\Procurement\Services;

use Modules\Procurement\Models\PurchaseOrder;

class PurchaseOrderDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(PurchaseOrder $purchaseOrder): array
    {
        $purchaseOrder->load([
            'items.procurementItem',
            'vendor',
            'approver',
            'requestForQuotation.purchaseRequisition',
        ]);

        return [
            'purchaseOrder' => $purchaseOrder,
            'showSignature' => true,
            'signatureLabel' => 'Pengadaan',
        ];
    }

    public function filename(PurchaseOrder $purchaseOrder): string
    {
        return sprintf('PurchaseOrder_%s.pdf', str_replace(' ', '_', $purchaseOrder->po_number ?? (string) $purchaseOrder->getKey()));
    }
}
