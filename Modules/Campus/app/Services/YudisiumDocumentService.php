<?php

namespace Modules\Campus\Services;

use Modules\Campus\Models\Yudisium;

class YudisiumDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Yudisium $yudisium): array
    {
        $yudisium->load(['organization', 'academicYear']);

        return [
            'yudisium' => $yudisium,
            'showSignature' => true,
            'signatureLabel' => 'Ketua Senat / Rektor',
        ];
    }

    public function filename(Yudisium $yudisium): string
    {
        $slug = preg_replace('/[^\w\-]+/u', '_', $yudisium->name ?? 'yudisium') ?: 'yudisium';

        return sprintf('Yudisium_%s.pdf', $slug);
    }
}
