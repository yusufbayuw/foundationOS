<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Employee\Models\LeaveRequest;
use Modules\Employee\Services\LeaveRequestDocumentService;
use Symfony\Component\HttpFoundation\Response;

class LeaveRequestPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(LeaveRequest $leaveRequest, LeaveRequestDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $leaveRequest,
            'employee::pdf.leave-request',
            $service->assemble($leaveRequest),
            $service->filename($leaveRequest),
        );
    }
}
