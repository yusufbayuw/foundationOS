<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\StudentInvoice;

class StudentInvoiceDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(StudentInvoice $invoice): array
    {
        $invoice->load(['items.tuitionType', 'invoiceable', 'tuitionType', 'payments']);

        return [
            'invoice' => $invoice,
            'recipientName' => $this->resolveRecipientName($invoice),
            'showSignature' => true,
            'signatureLabel' => 'Finance',
        ];
    }

    public function filename(StudentInvoice $invoice): string
    {
        return sprintf('StudentInvoice_%s.pdf', str_replace(' ', '_', $invoice->invoice_number ?? (string) $invoice->getKey()));
    }

    protected function resolveRecipientName(StudentInvoice $invoice): string
    {
        $invoiceable = $invoice->invoiceable;

        if ($invoiceable === null) {
            return '-';
        }

        if (method_exists($invoiceable, 'user') && $invoiceable->user) {
            return (string) ($invoiceable->user->name ?? '-');
        }

        if (isset($invoiceable->name)) {
            return (string) $invoiceable->name;
        }

        return '-';
    }
}
