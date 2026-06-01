<?php

namespace Modules\Core\Http\Controllers\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\Pdf\PdfDocumentRenderer;
use Modules\Core\Support\Pdf\TenantDocumentContext;
use Modules\Printing\Services\PrintableDocumentService;
use Symfony\Component\HttpFoundation\Response;

trait RendersTenantPdf
{
    use AuthorizesRequests;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function downloadTenantPdf(
        Model $record,
        string $view,
        array $data,
        string $filename,
        ?Tenant $tenant = null,
        ?Organization $organization = null,
        string $paper = 'a4',
        string $orientation = 'portrait',
    ): Response {
        $this->authorizePrint($record);

        if (method_exists($record, 'isPrintable') && ! $record->isPrintable()) {
            throw new AuthorizationException('This document cannot be printed in its current status.');
        }

        $tenant ??= Tenant::query()->findOrFail($record->getAttribute('tenant_id'));
        $context = TenantDocumentContext::resolve($tenant, $organization);

        return app(PdfDocumentRenderer::class)->download(
            $view,
            $data,
            $context,
            $filename,
            $paper,
            $orientation,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function downloadTenantPdfWithTemplate(
        Model $record,
        string $templateCode,
        array $data,
        string $filename,
        ?Tenant $tenant = null,
        ?Organization $organization = null,
    ): Response {
        $this->authorizePrint($record);

        if (method_exists($record, 'isPrintable') && ! $record->isPrintable()) {
            throw new AuthorizationException('This document cannot be printed in its current status.');
        }

        $tenant ??= Tenant::query()->findOrFail($record->getAttribute('tenant_id'));
        $context = TenantDocumentContext::resolve($tenant, $organization);

        return app(PrintableDocumentService::class)->download(
            $templateCode,
            $record,
            $data,
            $context,
            $filename,
        );
    }

    protected function authorizePrint(Model $record): void
    {
        $this->authorize('print', $record);
    }
}
