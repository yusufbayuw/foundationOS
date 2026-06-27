<?php

namespace Modules\Sales\Services;

use App\Support\TypedValue;
use Modules\Sales\Models\SalesOrder;

class SalesOrderDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(SalesOrder $salesOrder): array
    {
        $salesOrder->load(['customer', 'items']);

        return [
            'salesOrder' => $salesOrder,
            'showSignature' => true,
            'signatureLabel' => 'Sales',
        ];
    }

    public function filename(SalesOrder $salesOrder): string
    {
        return sprintf('SalesOrder_%s.pdf', str_replace(' ', '_', $salesOrder->order_number ?? TypedValue::string($salesOrder->getKey())));
    }
}
