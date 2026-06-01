<?php

namespace Modules\Procurement\Services;

use Modules\Procurement\Models\VendorBill;

class VendorBillDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(VendorBill $bill): array
    {
        $bill->load([
            'items.purchaseOrderItem.procurementItem',
            'vendor',
            'purchaseOrder',
            'goodsReceipt',
            'processor',
        ]);

        return [
            'bill' => $bill,
            'showSignature' => true,
            'signatureLabel' => 'Keuangan',
        ];
    }

    public function filename(VendorBill $bill): string
    {
        return sprintf('VendorBill_%s.pdf', str_replace(' ', '_', $bill->bill_number ?? (string) $bill->getKey()));
    }
}
