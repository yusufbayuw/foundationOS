<?php

namespace Modules\Finance\Services;

use App\Support\TypedValue;
use Modules\Finance\Models\CustomerInvoice;

class CustomerInvoiceDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(CustomerInvoice $invoice): array
    {
        $invoice->load(['items', 'organization']);

        return [
            'invoice' => $invoice,
            'showSignature' => true,
            'signatureLabel' => 'Finance',
        ];
    }

    public function filename(CustomerInvoice $invoice): string
    {
        return sprintf('CustomerInvoice_%s.pdf', str_replace(' ', '_', $invoice->invoice_number ?? TypedValue::string($invoice->getKey())));
    }
}
