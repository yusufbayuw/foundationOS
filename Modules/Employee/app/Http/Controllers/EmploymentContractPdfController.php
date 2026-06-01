<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Employee\Models\EmploymentContract;
use Modules\Employee\Services\EmploymentContractDocumentService;

class EmploymentContractPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(EmploymentContract $employmentContract, EmploymentContractDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $employmentContract,
            'employee::pdf.employment-contract',
            $service->assemble($employmentContract),
            $service->filename($employmentContract),
        );
    }
}
