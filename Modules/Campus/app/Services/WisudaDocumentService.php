<?php

namespace Modules\Campus\Services;

use Modules\Campus\Models\Wisuda;

class WisudaDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Wisuda $wisuda): array
    {
        $wisuda->load(['yudisium.organization', 'yudisium.academicYear']);

        return [
            'wisuda' => $wisuda,
            'yudisium' => $wisuda->yudisium,
            'showSignature' => true,
            'signatureLabel' => 'Wakil Rektor / Ketua Panitia',
        ];
    }

    public function filename(Wisuda $wisuda): string
    {
        $slug = preg_replace('/[^\w\-]+/u', '_', $wisuda->name ?? 'wisuda') ?: 'wisuda';

        return sprintf('Wisuda_%s.pdf', $slug);
    }
}
