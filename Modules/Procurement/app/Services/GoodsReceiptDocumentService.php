<?php

namespace Modules\Procurement\Services;

use App\Support\TypedValue;
use Modules\Procurement\Models\GoodsReceipt;

class GoodsReceiptDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(GoodsReceipt $receipt): array
    {
        $receipt->load([
            'items.purchaseOrderItem.procurementItem',
            'purchaseOrder.vendor',
            'receiver',
            'inspector',
        ]);

        return [
            'receipt' => $receipt,
            'showSignature' => true,
            'signatureLabel' => 'Penerimaan Barang',
        ];
    }

    public function filename(GoodsReceipt $receipt): string
    {
        return sprintf('GoodsReceipt_%s.pdf', str_replace(' ', '_', $receipt->receipt_number ?? TypedValue::string($receipt->getKey())));
    }
}
