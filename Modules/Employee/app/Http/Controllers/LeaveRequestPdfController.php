<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Employee\Models\LeaveRequest;
use Modules\Employee\Services\LeaveRequestDocumentService;

class LeaveRequestPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(LeaveRequest $leaveRequest, LeaveRequestDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $leaveRequest,
            'employee::pdf.leave-request',
            $service->assemble($leaveRequest),
            $service->filename($leaveRequest),
        );
    }
}
