<?php

namespace Modules\Boarding\Services;

use App\Support\TypedValue;
use Modules\Boarding\Models\BoardingLeavePermit;

class BoardingLeavePermitDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(BoardingLeavePermit $permit): array
    {
        $permit->load(['organization']);

        return [
            'permit' => $permit,
            'showSignature' => true,
            'signatureLabel' => 'Wali Asrama',
        ];
    }

    public function filename(BoardingLeavePermit $permit): string
    {
        return sprintf('BoardingLeavePermit_%s.pdf', str_replace(' ', '_', $permit->code ?? TypedValue::string($permit->getKey())));
    }
}
