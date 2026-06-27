<?php

namespace Modules\Enrollment\Services;

use App\Support\TypedValue;
use Modules\Enrollment\Models\Registration;

class RegistrationDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Registration $registration): array
    {
        $registration->load([
            'applicant.admissionPeriod',
            'applicant.acceptedProgram',
            'completedBy',
        ]);

        return [
            'registration' => $registration,
            'applicant' => $registration->applicant,
            'showSignature' => true,
            'signatureLabel' => 'Petugas Pendaftaran',
        ];
    }

    public function filename(Registration $registration): string
    {
        $applicantNumber = $registration->applicant->registration_number ?? TypedValue::string($registration->getKey());

        return sprintf('Bukti_Daftar_Ulang_%s.pdf', str_replace(' ', '_', $applicantNumber));
    }
}
