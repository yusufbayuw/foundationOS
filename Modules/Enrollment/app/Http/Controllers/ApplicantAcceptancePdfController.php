<?php

namespace Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Services\ApplicantAcceptanceDocumentService;

class ApplicantAcceptancePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Applicant $applicant, ApplicantAcceptanceDocumentService $service)
    {
        if (! $applicant->canPrintAcceptanceLetter()) {
            throw new AuthorizationException('This document cannot be printed in its current status.');
        }

        return $this->downloadTenantPdf(
            $applicant,
            'enrollment::pdf.acceptance-letter',
            $service->assemble($applicant),
            $service->filename($applicant),
        );
    }
}
