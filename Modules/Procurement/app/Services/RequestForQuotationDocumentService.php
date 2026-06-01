<?php

namespace Modules\Procurement\Services;

use Modules\Procurement\Models\RequestForQuotation;

class RequestForQuotationDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(RequestForQuotation $rfq): array
    {
        $rfq->load([
            'items.procurementItem',
            'vendors.vendor',
            'purchaseRequisition.requester',
            'creator',
        ]);

        return [
            'rfq' => $rfq,
            'showSignature' => true,
            'signatureLabel' => 'Pengadaan',
        ];
    }

    public function filename(RequestForQuotation $rfq): string
    {
        return sprintf('RequestForQuotation_%s.pdf', str_replace(' ', '_', $rfq->rfq_number ?? (string) $rfq->getKey()));
    }
}
