<?php

namespace Modules\Consulting\Services;

use App\Support\TypedValue;
use Modules\Consulting\Models\EngagementInvoice;

class EngagementInvoiceDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(EngagementInvoice $invoice): array
    {
        $invoice->load(['organization']);

        return [
            'invoice' => $invoice,
            'showSignature' => true,
            'signatureLabel' => 'Consulting',
        ];
    }

    public function filename(EngagementInvoice $invoice): string
    {
        return sprintf('EngagementInvoice_%s.pdf', str_replace(' ', '_', $invoice->code ?? TypedValue::string($invoice->getKey())));
    }
}
