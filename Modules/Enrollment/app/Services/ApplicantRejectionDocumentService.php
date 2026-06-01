<?php

namespace Modules\Enrollment\Services;

use Modules\Enrollment\Models\Applicant;

class ApplicantRejectionDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Applicant $applicant): array
    {
        $applicant->load([
            'admissionPeriod.organization',
            'firstProgramChoice',
            'secondProgramChoice',
        ]);

        return [
            'applicant' => $applicant,
            'admissionPeriod' => $applicant->admissionPeriod,
            'showSignature' => true,
            'signatureLabel' => 'Kepala Sekolah / Panitia PPDB',
        ];
    }

    public function filename(Applicant $applicant): string
    {
        $number = str_replace(' ', '_', $applicant->registration_number ?? (string) $applicant->getKey());

        return sprintf('Surat_Penolakan_%s.pdf', $number);
    }
}
