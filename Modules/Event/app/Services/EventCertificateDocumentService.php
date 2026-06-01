<?php

namespace Modules\Event\Services;

use Modules\Event\Models\EventCertificate;

class EventCertificateDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(EventCertificate $certificate): array
    {
        $certificate->load(['organization']);

        return [
            'certificate' => $certificate,
            'showSignature' => true,
            'signatureLabel' => 'Penyelenggara Acara',
        ];
    }

    public function filename(EventCertificate $certificate): string
    {
        return sprintf('EventCertificate_%s.pdf', str_replace(' ', '_', $certificate->code ?? (string) $certificate->getKey()));
    }
}
