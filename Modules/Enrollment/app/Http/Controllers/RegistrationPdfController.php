<?php

namespace Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Enrollment\Models\Registration;
use Modules\Enrollment\Services\RegistrationDocumentService;

class RegistrationPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Registration $registration, RegistrationDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $registration,
            'enrollment::pdf.registration-proof',
            $service->assemble($registration),
            $service->filename($registration),
        );
    }
}
