<?php

namespace Modules\Boarding\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Boarding\Models\BoardingLeavePermit;
use Modules\Boarding\Services\BoardingLeavePermitDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;

class BoardingLeavePermitPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(BoardingLeavePermit $boardingLeavePermit, BoardingLeavePermitDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $boardingLeavePermit,
            'boarding::pdf.leave-permit',
            $service->assemble($boardingLeavePermit),
            $service->filename($boardingLeavePermit),
            organization: $boardingLeavePermit->organization,
        );
    }
}
