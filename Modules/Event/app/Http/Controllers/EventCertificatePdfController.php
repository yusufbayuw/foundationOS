<?php

namespace Modules\Event\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Event\Models\EventCertificate;
use Modules\Event\Services\EventCertificateDocumentService;
use Symfony\Component\HttpFoundation\Response;

class EventCertificatePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(EventCertificate $eventCertificate, EventCertificateDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $eventCertificate,
            'event::pdf.event-certificate',
            $service->assemble($eventCertificate),
            $service->filename($eventCertificate),
            organization: $eventCertificate->organization,
            orientation: 'landscape',
        );
    }
}
