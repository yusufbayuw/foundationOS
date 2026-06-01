<?php

namespace Modules\Library\Services;

use Modules\Library\Models\Loan;

class LoanDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Loan $loan): array
    {
        $loan->load(['member', 'bookCopy.book', 'processedBy']);

        return [
            'loan' => $loan,
            'showSignature' => true,
            'signatureLabel' => 'Petugas Perpustakaan',
        ];
    }

    public function filename(Loan $loan): string
    {
        return sprintf('Loan_%s.pdf', (string) $loan->getKey());
    }
}
