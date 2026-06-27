<?php

namespace Modules\Enrollment\Services;

use App\Support\TypedValue;
use Modules\Enrollment\Models\Applicant;

class ApplicantAcceptanceDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Applicant $applicant): array
    {
        $applicant->load([
            'admissionPeriod.organization',
            'acceptedProgram',
            'firstProgramChoice',
            'secondProgramChoice',
        ]);

        return [
            'applicant' => $applicant,
            'admissionPeriod' => $applicant->admissionPeriod,
            'acceptedProgram' => $applicant->acceptedProgram ?? $applicant->firstProgramChoice,
            'showSignature' => true,
            'signatureLabel' => 'Kepala Sekolah / Panitia PPDB',
        ];
    }

    public function filename(Applicant $applicant): string
    {
        $number = str_replace(' ', '_', $applicant->registration_number ?? TypedValue::string($applicant->getKey()));

        return sprintf('Surat_Penerimaan_%s.pdf', $number);
    }
}
