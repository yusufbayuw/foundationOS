<?php

namespace Modules\Printing\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;
use Modules\Core\Support\Pdf\PdfDocumentRenderer;
use Modules\Core\Support\Pdf\TenantDocumentContext;
use Modules\Monitoring\Models\PrintExportLog;

class PrintableDocumentService
{
    public function __construct(
        private readonly PrintTemplateResolver $templateResolver,
        private readonly PdfDocumentRenderer $renderer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function download(
        string $templateCode,
        Model $subject,
        array $data,
        TenantDocumentContext $context,
        string $filename,
        ?User $user = null,
    ): Response {
        $template = $this->templateResolver->resolve($templateCode, (int) $subject->getAttribute('tenant_id'));

        $sanitizedFilename = $this->renderer->sanitizeFilename($filename);

        $response = $this->renderer->download(
            $template->view,
            $data,
            $context,
            $sanitizedFilename,
            $template->paper,
            $template->orientation,
        );

        PrintExportLog::query()->create([
            'tenant_id' => $subject->getAttribute('tenant_id'),
            'user_id' => ($user ?? Auth::user())?->getKey(),
            'template_code' => $templateCode,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'filename' => $sanitizedFilename,
            'exported_at' => now(),
        ]);

        return $response;
    }
}
