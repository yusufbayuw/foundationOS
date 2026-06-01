<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Services\StudentInvoiceDocumentService;

class StudentInvoicePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(StudentInvoice $studentInvoice, StudentInvoiceDocumentService $service)
    {
        return $this->downloadTenantPdfWithTemplate(
            $studentInvoice,
            'finance_student_invoice',
            $service->assemble($studentInvoice),
            $service->filename($studentInvoice),
        );
    }
}
