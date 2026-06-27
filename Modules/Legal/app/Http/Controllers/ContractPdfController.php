<?php

namespace Modules\Legal\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Legal\Models\Contract;
use Modules\Legal\Services\ContractDocumentService;
use Symfony\Component\HttpFoundation\Response;

class ContractPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Contract $contract, ContractDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $contract,
            'legal::pdf.contract',
            $service->assemble($contract),
            $service->filename($contract),
            organization: $contract->organization,
        );
    }
}
