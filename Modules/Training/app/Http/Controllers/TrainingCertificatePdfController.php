<?php

namespace Modules\Training\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Training\Models\TrainingCertificate;
use Modules\Training\Services\TrainingCertificateDocumentService;
use Symfony\Component\HttpFoundation\Response;

class TrainingCertificatePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(TrainingCertificate $trainingCertificate, TrainingCertificateDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $trainingCertificate,
            'training::pdf.training-certificate',
            $service->assemble($trainingCertificate),
            $service->filename($trainingCertificate),
            orientation: 'landscape',
        );
    }
}
