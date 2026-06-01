<?php

namespace Modules\Legal\Services;

use Modules\Legal\Models\Contract;

class ContractDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Contract $contract): array
    {
        $contract->load(['organization']);

        return [
            'contract' => $contract,
            'showSignature' => true,
            'signatureLabel' => 'Legal',
        ];
    }

    public function filename(Contract $contract): string
    {
        return sprintf('Contract_%s.pdf', str_replace(' ', '_', $contract->code ?? (string) $contract->getKey()));
    }
}
