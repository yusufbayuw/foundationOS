<?php

namespace Modules\Property\Services;

use Modules\Property\Models\LeaseInvoice;

class LeaseInvoiceDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(LeaseInvoice $invoice): array
    {
        $invoice->load(['organization']);

        return [
            'invoice' => $invoice,
            'showSignature' => true,
            'signatureLabel' => 'Property Management',
        ];
    }

    public function filename(LeaseInvoice $invoice): string
    {
        return sprintf('LeaseInvoice_%s.pdf', str_replace(' ', '_', $invoice->code ?? (string) $invoice->getKey()));
    }
}
