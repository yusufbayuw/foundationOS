<?php

namespace Modules\Training\Services;

use Modules\Training\Models\TrainingCertificate;

class TrainingCertificateDocumentService
{
    public function __construct(
        private readonly TrainingCertificateService $certificateService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function assemble(TrainingCertificate $certificate): array
    {
        $certificate->load(['enrollment.batch.program']);

        return [
            'certificate' => $certificate,
            'enrollment' => $certificate->enrollment,
            'qrSvg' => $this->certificateService->qrSvg($certificate),
            'showSignature' => true,
            'signatureLabel' => 'Penyelenggara Pelatihan',
        ];
    }

    public function filename(TrainingCertificate $certificate): string
    {
        return sprintf('TrainingCertificate_%s.pdf', str_replace(' ', '_', $certificate->certificate_number ?? (string) $certificate->getKey()));
    }
}
