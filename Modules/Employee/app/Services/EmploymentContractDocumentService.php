<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\EmploymentContract;

class EmploymentContractDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(EmploymentContract $contract): array
    {
        $contract->load(['employee']);

        return [
            'contract' => $contract,
            'showSignature' => true,
            'signatureLabel' => 'HR / Karyawan',
        ];
    }

    public function filename(EmploymentContract $contract): string
    {
        return sprintf('EmploymentContract_%s.pdf', str_replace(' ', '_', $contract->contract_number ?? (string) $contract->getKey()));
    }
}
