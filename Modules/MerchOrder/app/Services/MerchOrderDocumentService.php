<?php

namespace Modules\MerchOrder\Services;

use App\Support\TypedValue;
use Modules\MerchOrder\Models\MerchOrder;

class MerchOrderDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(MerchOrder $merchOrder): array
    {
        $merchOrder->load(['organization']);

        return [
            'merchOrder' => $merchOrder,
            'showSignature' => true,
            'signatureLabel' => 'Merchandise',
        ];
    }

    public function filename(MerchOrder $merchOrder): string
    {
        return sprintf('MerchOrder_%s.pdf', str_replace(' ', '_', $merchOrder->code ?? TypedValue::string($merchOrder->getKey())));
    }
}
