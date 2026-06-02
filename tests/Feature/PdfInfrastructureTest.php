<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Support\Pdf\PdfDocumentRenderer;
use Modules\Core\Support\Pdf\TenantDocumentContext;
use Tests\TestCase;

class PdfInfrastructureTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_tenant_document_context_resolves_branding_settings(): void
    {
        $tenant = $this->makeTenant();
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Sekolah Utama',
            'address' => 'Jl. Pendidikan No. 1',
            'is_main' => true,
        ]);

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'branding',
            'key' => 'primary_color',
            'value' => '#ff0000',
            'type' => 'string',
        ]);

        $context = TenantDocumentContext::resolve($tenant, $organization);

        $this->assertSame('#ff0000', $context->primaryColor);
        $this->assertSame('Sekolah Utama', $context->institutionName);
        $this->assertSame('Jl. Pendidikan No. 1', $context->address);
    }

    public function test_shared_pdf_layout_renders_institution_header(): void
    {
        $tenant = $this->makeTenant();
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Foundation Academy',
            'is_main' => true,
        ]);

        $context = TenantDocumentContext::resolve($tenant, $organization);

        $html = View::make('core::pdf.test-stub', $context->toViewData())->render();

        $this->assertStringContainsString('FOUNDATION ACADEMY', $html);
        $this->assertStringContainsString('Body content for PDF infrastructure test.', $html);
    }

    public function test_pdf_document_renderer_sanitizes_filename(): void
    {
        $renderer = app(PdfDocumentRenderer::class);

        $this->assertSame(
            'Invoice_2026_001.pdf',
            $renderer->sanitizeFilename('Invoice 2026/001.pdf'),
        );
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-pdf',
            'name' => 'PDF Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
