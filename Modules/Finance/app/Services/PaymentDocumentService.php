<?php

namespace Modules\Finance\Services;

use App\Support\TypedValue;
use Modules\Finance\Models\Payment;

class PaymentDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Payment $payment): array
    {
        $payment->load(['studentInvoice.invoiceable', 'studentInvoice.items', 'verifiedBy']);

        return [
            'payment' => $payment,
            'invoice' => $payment->studentInvoice,
            'showSignature' => true,
            'signatureLabel' => 'Verified By',
        ];
    }

    public function filename(Payment $payment): string
    {
        return sprintf('PaymentReceipt_%s.pdf', str_replace(' ', '_', $payment->payment_number ?? TypedValue::string($payment->getKey())));
    }
}
