<?php

namespace Modules\Core\Support\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PdfDocumentRenderer
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function download(
        string $view,
        array $data,
        TenantDocumentContext $context,
        string $filename,
        string $paper = 'a4',
        string $orientation = 'portrait',
    ): Response {
        $pdf = $this->render($view, $data, $context, $paper, $orientation);

        return $pdf->download($this->sanitizeFilename($filename));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(
        string $view,
        array $data,
        TenantDocumentContext $context,
        string $paper = 'a4',
        string $orientation = 'portrait',
    ): \Barryvdh\DomPDF\PDF {
        $previousLocale = app()->getLocale();
        app()->setLocale($context->locale);

        try {
            return Pdf::loadView($view, array_merge($context->toViewData(), $data))
                ->setPaper($paper, $orientation);
        } finally {
            app()->setLocale($previousLocale);
        }
    }

    public function sanitizeFilename(string $filename): string
    {
        $sanitized = preg_replace('/[^\w\-_.]+/u', '_', $filename) ?? 'document.pdf';

        if (! str_ends_with(strtolower($sanitized), '.pdf')) {
            $sanitized .= '.pdf';
        }

        return $sanitized;
    }

    /**
     * @param  list<array{view: string, data: array<string, mixed>, filename: string}>  $documents
     */
    public function downloadZip(array $documents, TenantDocumentContext $context, string $zipFilename): SymfonyResponse
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'fos_pdf_zip_');
        $zip = new \ZipArchive;

        if ($zipPath === false || $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create PDF archive.');
        }

        foreach ($documents as $document) {
            $pdf = $this->render(
                $document['view'],
                $document['data'],
                $context,
                $document['paper'] ?? 'a4',
                $document['orientation'] ?? 'portrait',
            );

            $zip->addFromString(
                $this->sanitizeFilename($document['filename']),
                $pdf->output(),
            );
        }

        $zip->close();

        return response()->download($zipPath, $this->sanitizeFilename(str_replace('.pdf', '.zip', $zipFilename)))
            ->deleteFileAfterSend(true);
    }
}
