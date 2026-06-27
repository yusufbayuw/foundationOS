<?php

namespace Modules\Library\Services;

use App\Support\TypedValue;
use Modules\Library\Models\Fine;

class FineDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Fine $fine): array
    {
        $fine->load(['loan.member', 'loan.bookCopy.book']);

        return [
            'fine' => $fine,
            'showSignature' => true,
            'signatureLabel' => 'Petugas Perpustakaan',
        ];
    }

    public function filename(Fine $fine): string
    {
        return sprintf('Fine_%s.pdf', TypedValue::string($fine->getKey()));
    }
}
