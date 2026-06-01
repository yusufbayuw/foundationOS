<?php

namespace Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Services\ApplicantRejectionDocumentService;

class ApplicantRejectionPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Applicant $applicant, ApplicantRejectionDocumentService $service)
    {
        if (! $applicant->canPrintRejectionLetter()) {
            throw new AuthorizationException('This document cannot be printed in its current status.');
        }

        return $this->downloadTenantPdf(
            $applicant,
            'enrollment::pdf.rejection-letter',
            $service->assemble($applicant),
            $service->filename($applicant),
        );
    }
}
